@extends('master')

@section('css')
@stop

@section('content')
<div id="markdown">
<h2>Markdown guide</h2>
<p>Here is an overview of Markdown syntax that you can use on Codeblock.</p>
<h3>Headers</h3><pre><code class="markdown"># This is an &lt;h1&gt; tag
## This is an &lt;h2&gt; tag
###### This is an &lt;h6&gt; tag</code></pre>
<h3>Emphasis</h3>
<pre><code class="markdown">*This text will be italic*
_This will also be italic_
**This text will be bold**
__This will also be bold__
*You **can** combine them*
</code></pre>
<h3>Unordered lists</h3>
<pre><code class="markdown">* Item 1
* Item 2
* Item 2a
* Item 2b
</code></pre>
<h3>Ordered lists</h3>
<pre><code class="markdown">1. Item 1
2. Item 2
3. Item 3
* Item 3a
* Item 3b
</code></pre>
<!--
<h3>Images</h3>
<pre><code class="markdown">![Codeblock Logo](/img/favicon.png)</code></pre>
-->
<h3>Links</h3>
<pre><code class="markdown">[Codeblock](https://{{strtolower($siteName)}})</code></pre>
<h3>Blockquotes</h3>
<pre><code class="markdown">As Kanye West said:
&gt; We're living the future so
&gt; the present is our past.
</code></pre>
<h3>Inline code</h3>
<pre><code>I think you should use an
`&lt;addr&gt;` element here instead.
</code></pre>
</div>
@stop

@section('script')
@stop