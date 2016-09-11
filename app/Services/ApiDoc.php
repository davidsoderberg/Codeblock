<?php namespace App\Services;

use Crada\Apidoc\Extractor;
use Illuminate\Support\Facades\View;

class ApiDoc
{
    /**
     * Classes collection
     *
     * @var array
     */
    protected $_st_classes;

    /**
     * Constructor
     *
     * @param array $st_classes
     */
    public function __construct(array $st_classes)
    {
        $this->_st_classes = $st_classes;
    }

    public function generate()
    {
        return $this->generateTemplate();
    }

    /**
     * Generate the content of the documentation
     *
     * @return boolean
     */
    protected function generateTemplate()
    {
        $st_annotations = $this->extractAnnotations();

        $template = [];
        $counter  = 0;
        $section  = null;

        foreach ($st_annotations as $class => $methods) {
            foreach ($methods as $name => $docs) {
                if (isset($docs['ApiDescription'][0]['section'])) {
                    $section = $docs['ApiDescription'][0]['section'];
                } elseif (isset($docs['ApiSector'][0]['name'])) {
                    $section = $docs['ApiSector'][0]['name'];
                } else {
                    $section = $class;
                }
                if (0 === count($docs)) {
                    continue;
                }

                $sampleOutput = $this->generateSampleOutput($docs, $counter);

                $tr                   = [
                    'elt_id' => $counter,
                    'method' => View::make('doc.api.badge')->with(['method' => strtolower($docs['ApiMethod'][0]['type'])])->render(),
                    'route' => $docs['ApiRoute'][0]['name'],
                    'description' => $docs['ApiDescription'][0]['description'],
                    'headers' => $this->generateHeadersTemplate($counter, $docs),
                    'parameters' => $this->generateParamsTemplate($counter, $docs),
                    'body' => $this->generateBodyTemplate($counter, $docs),
                    'sandbox_form' => $this->generateSandboxForm($docs, $counter),
                    'sample_response_headers' => $sampleOutput[0],
                    'sample_response_body' => $sampleOutput[1]
                ];
                $template[$section][] = View::make('doc.api.main')->with($tr)->render();
                $counter++;
            }
        }

        $output = '';

        foreach ($template as $key => $value) {
            array_unshift($value, '<h2>' . $key . '</h2>');
            $output .= implode(PHP_EOL, $value);
        }

        return [
            'content' => $output,
            'date' => date('Y-m-d, H:i:s'),
            'url' => env('URL'),
        ];
    }

    /**
     * Extract annotations
     *
     * @return array
     */
    protected function extractAnnotations()
    {
        foreach ($this->_st_classes as $class) {
            $st_output[] = Extractor::getAllClassAnnotations($class);
        }

        return end($st_output);
    }

    /**
     * Generate the sample output
     *
     * @param  array   $st_params
     * @param  integer $counter
     *
     * @return string
     */
    protected function generateSampleOutput($st_params, $counter)
    {

        if ( ! isset($st_params['ApiReturn'])) {
            $responseBody = '';
        } else {
            $ret = [];
            foreach ($st_params['ApiReturn'] as $params) {
                if (in_array($params['type'], [
                        'object',
                        'array(object) ',
                        'array',
                        'string',
                        'boolean',
                        'integer',
                        'number'
                    ]) && isset($params['sample'])
                ) {
                    $tr = [
                        'elt_id' => $counter,
                        'response' => $params['sample'],
                        'description' => '',
                    ];
                    if (isset($params['description'])) {
                        $tr['description'] = $params['description'];
                    }
                    $ret[] = View::make('doc.api.sampleResponse')->with($tr)->render();
                }
            }

            $responseBody = implode(PHP_EOL, $ret);
        }

        if ( ! isset($st_params['ApiReturnHeaders'])) {
            $responseHeaders = '';
        } else {
            $ret = [];
            foreach ($st_params['ApiReturnHeaders'] as $headers) {
                if (isset($headers['sample'])) {
                    $tr = [
                        'elt_id' => $counter,
                        'response' => $headers['sample'],
                        'description' => ''
                    ];

                    $ret[] = View::make('doc.api.sampleResponseHeader')->with($tr)->render();
                }
            }

            $responseHeaders = implode(PHP_EOL, $ret);
        }

        return [$responseHeaders, $responseBody];
    }

    /**
     * Generates the template for headers
     *
     * @param  int   $id
     * @param  array $st_params
     *
     * @return void|string
     */
    protected function generateHeadersTemplate($id, $st_params)
    {
        if ( ! isset($st_params['ApiHeaders'])) {
            return;
        }

        $body = [];
        foreach ($st_params['ApiHeaders'] as $params) {
            $tr     = [
                'name' => $params['name'],
                'type' => $params['type'],
                'nullable' => @$params['nullable'] == '1' ? 'No' : 'Yes',
                'description' => @$params['description'],
            ];
            $body[] = View::make('doc.api.paramContent')->with($tr)->render();
        }

        return View::make('doc.api.paramTable')->with(['tbody' => implode(PHP_EOL, $body)])->render();
    }

    /**
     * Generates the template for parameters
     *
     * @param  int   $id
     * @param  array $st_params
     *
     * @return void|string
     */
    protected function generateParamsTemplate($id, $st_params)
    {
        if ( ! isset($st_params['ApiParams'])) {
            return;
        }

        $body = [];
        foreach ($st_params['ApiParams'] as $params) {
            $tr = [
                'name' => $params['name'],
                'type' => $params['type'],
                'nullable' => @$params['nullable'] == '1' ? 'No' : 'Yes',
                'description' => @$params['description'],
            ];
            if (in_array($params['type'], ['object', 'array(object) ', 'array']) && isset($params['sample'])) {
                $tr['type'] .= ' ' . strtr(static::$paramSampleBtnTpl, ['sample' => $params['sample']]);
            }
            $body[] = View::make('doc.api.paramContent')->with($tr)->render();
        }

        return View::make('doc.api.paramTable')->with(['tbody' => implode(PHP_EOL, $body)])->render();
    }

    /**
     * Generate POST body template
     *
     * @param  int   $id
     * @param  array $docs
     *
     * @return void|string
     */
    private function generateBodyTemplate($id, $docs)
    {
        if ( ! isset($docs['ApiBody'])) {
            return;
        }

        $body = $docs['ApiBody'][0];

        return View::make('doc.api.samplePostBody')->with([
            'elt_id' => $id,
            'body' => $body['sample']
        ])->render();

    }

    /**
     * Generate route paramteres form
     *
     * @param  array   $st_params
     * @param  integer $counter
     *
     * @return void|mixed
     */
    protected function generateSandboxForm($st_params, $counter)
    {
        $headers = [];
        $params  = [];

        if (isset($st_params['ApiParams']) && is_array($st_params['ApiParams'])) {
            foreach ($st_params['ApiParams'] as $param) {
                $params[] = View::make('doc.api.sandboxFormInput')->with(['name' => $param['name']])->render();
            }
        }

        if (isset($st_params['ApiHeaders']) && is_array($st_params['ApiHeaders'])) {
            foreach ($st_params['ApiHeaders'] as $header) {
                $headers[] = View::make('doc.api.sandboxFormInput')->with(['name' => $header['name']])->render();
            }
        }

        $tr = [
            'elt_id' => $counter,
            'method' => $st_params['ApiMethod'][0]['type'],
            'route' => $st_params['ApiRoute'][0]['name'],
            'headers' => implode(PHP_EOL, $headers),
            'params' => implode(PHP_EOL, $params),
        ];

        return View::make('doc.api.sandboxForm')->with($tr)->render();
    }
}