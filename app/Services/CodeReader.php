<?php namespace App\Services;

/**
 * Class CodeReader
 * @package App\Services
 */
final class CodeReader
{

    /**
     * Property to store all info about all classes that should be documented.
     *
     * @var array
     */
    private $classes = [];

    /**
     * Property to store paths to files with code that should be documented.
     *
     * @var array
     */
    private $paths = [];

    /**
     * Property to store DocBlock object in.
     *
     * @var DocBlock
     */
    private $docblock;

    /**
     * Reflector constructor.
     *
     * @param $path
     */
    public function __construct($path)
    {
        $this->getPaths($path);
        $this->docblock = new DocBlock();
        $this->loopClasses();
    }

    /**
     * Get all file paths in current path and subpaths.
     *
     * @param $path
     */
    private function getPaths($path)
    {
        $di = new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS);
        $it = new \RecursiveIteratorIterator($di);

        foreach ($it as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) == "php") {
                $file          = str_replace(base_path() . '\\', '', $file->getRealPath());
                $file          = str_replace('.php', '', $file);
                $this->paths[] = ucfirst($file);
            }
        }
    }

    /**
     * Loops all paths and creates classes of them.
     */
    private function loopClasses()
    {
        foreach ($this->paths as $class) {
            if (class_exists($class)) {
                $this->create_class(new \ReflectionClass($class));
            }
        }
    }

    /**
     * Creates classes from namespace.
     *
     * @param $class
     */
    private function create_class(\ReflectionClass $class)
    {
        $class_name = $class->getName();
        if (in_array($class_name, $this->paths)) {
            $this->docblock->setComment($class->getDocComment());
            $this->classes[$class_name] = [
                'name' => $class->getShortName(),
                'namespace' => $class->getNamespaceName(),
                'desc' => $this->docblock->desc,
                'tags' => $this->getClassTags(),
                'properties' => $this->getProperties($class),
                'methods' => $this->getMethods($class),
                'interfaces' => $this->getInterfaces($class),
                'traits' => $this->getTraits($class),
                'isAbstract' => $class->isAbstract(),
                'isFinal' => $class->isFinal(),
                'isInterface' => $class->isInterface(),
                'isTrait' => $class->isTrait()
            ];
        }
    }

    /**
     * Fetching tags from DockBlock for class.
     *
     * @return Array
     */
    private function getClassTags()
    {
        $tags = $this->docblock->tags;
        if (isset($tags['package'])) {
            unset($tags['package']);
        }

        return $tags;
    }

    /**
     * Fetch property for current object.
     *
     * @param \ReflectionClass $object
     *
     * @return array
     */
    private function getProperties(\ReflectionClass $object)
    {
        $properties = [];
        foreach ($object->getProperties() as $property) {
            $this->docblock->setComment($property->getDocComment());
            $properties[$property->getName()] = [
                'desc' => $this->docblock->desc,
                'tags' => $this->docblock->tags,
                'modifier' => $this->getModifier($property)
            ];
        }
        return $properties;
    }

    /**
     * Fetch modifier for method or property.
     *
     * @param $object
     *
     * @return string
     */
    private function getModifier($object)
    {
        if ($object->isPrivate()) {
            return 'Private';
        } elseif ($object->isPublic()) {
            return 'Public';
        }

        return 'Protected';
    }

    /**
     * Fetch method for object.
     *
     * @param \ReflectionClass $object
     *
     * @return array
     */
    private function getMethods(\ReflectionClass $object)
    {
        $methods = [];

        foreach ($object->getMethods() as $method) {
            $this->docblock->setComment($method->getDocComment());
            $tags = $this->getMethodTags($method);

            $methods[$method->getName()] = [
                'desc' => $this->docblock->desc,
                'modifier' => $this->getModifier($method),
                'params' => $tags['params'],
                'return' => $tags['return'],
                'throws' => $tags['throws'],
                'abstract' => $method->isAbstract(),
                'static' => $method->isStatic()
            ];
        }

        return $methods;
    }

    /**
     * Fetching params, return och throw value from DocBlock for method and merges them with code from file.
     *
     * @return array
     */
    private function getMethodTags(\ReflectionMethod $method)
    {
        $tags = $this->docblock->tags;

        $params = [];
        foreach ($method->getParameters() as $parameter) {
            $name = '$'.$parameter->name;
            $params[$name] = [];
            $params[$name]['var'] = $name;
            $params[$name]['type'] = '';
            $params[$name]['desc'] = '';
            if ($parameter->isDefaultValueAvailable()) {
                $params[$name]['defaultValue'] = $parameter->getDefaultValue();
            }
        }

        if (isset($tags['param'])) {
            foreach ($tags['param'] as $param) {
                if (isset($param['var'])) {
                    $key = $param['var'];

                    if (!isset($params[$key]['var'])) {
                        $params[$key]['var'] = $key;
                    }

                    if (isset($param['desc'])) {
                        $params[$key]['desc'] = $param['desc'];
                    } else {
                        $params[$key]['desc'] = '';
                    }

                    if (isset($param['type'])) {
                        $params[$key]['type'] = $param['type'];
                    } else {
                        $params[$key]['type'] = '';
                    }
                }
            }
        }
        $tags['params'] = $params;

        if (! isset($tags['return'])) {
            $tags['return'] = [];
        }

        if (! isset($tags['throws'])) {
            $tags['throws'] = [];
        }

        return $tags;
    }

    /**
     * Fetch inteface for object.
     *
     * @param \ReflectionClass $object
     *
     * @return array
     */
    private function getInterfaces(\ReflectionClass $object)
    {
        $interfaces = [];
        foreach ($object->getInterfaces() as $interface) {
            $interfaces[] = $interface->getName();
            if (in_array($interface->getName(), $this->paths)) {
                $this->create_class($interface);
            }
        }
        return $interfaces;
    }

    /**
     * Fetch traits for object.
     *
     * @param \ReflectionClass $object
     *
     * @return array
     */
    private function getTraits(\ReflectionClass $object)
    {
        $traits = [];
        foreach ($object->getTraits() as $trait) {
            $traits[] = $trait->getName();
            $this->create_class($trait);
        }
        return $traits;
    }

    /**
     * Getter for all classes.
     *
     * @return array
     */
    public function getClasses()
    {
        return $this->classes;
    }
}
