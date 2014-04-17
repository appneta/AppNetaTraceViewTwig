AppNetaTraceViewTwig
====================
![A Symfony request profiled in TraceView.](http://appneta.github.io/AppNetaTraceViewBundle/images/AppNetaTraceViewBundle.png)

The `AppNeta\TraceViewTwig` package provides integration points between Twig and
[AppNeta TraceView](http://www.appneta.com/products/traceview/). It currently supports:

- Tracking Twig template rendering as profiles
- Injecting real user monitoring JavaScript (via Twig extension)

## Template Tracking
Twig template tracking is provided by replacing the Twig base template class with
a TraceView-augmented one. The easiest way to load the relevant base template
class is to add this package as a `require` in your `composer.json`:
```
    "require": {
        "php": ">=5.3.3",
        [...]
        "appneta/traceview-twig": "master@dev"
    },
```

If you're using Twig with Symfony2, you can add this line to the `twig` section
of your `config.yml`:
```
twig:
    base_template_class: AppNeta\TraceViewTwig\Template\TraceViewTwigTemplate
```

If you're manually constructing a Twig environment, you should instead use the
`base_template_class` option:
```
$options = array(
    'cache' => ...,
    'base_template_class' => AppNeta\TraceViewTwig\Template\TraceViewTwigTemplate
);
$twigEnv = new Twig_Environment($loader, $options);
```

## Real User Monitoring
TraceView real user monitoring JavaScript is usually added by calling the
`oboe_get_rum_header` and `oboe_get_rum_footer` functions. Because Twig templates
do not allow arbitrary PHP execution, this package provides a Twig extension that
exposes them as Twig functions.

If you're using Twig with Symfony2, you can load the extension from your `config.yml`:
```
    appneta.twig.traceview_twig_rum_extension:
        class: AppNeta\TraceViewTwig\Extension\TraceViewRUMExtension
        tags:
            - { name: twig.extension }
```

If you're manually constructing a Twig environment, you should instead call the
`addExtension` method directly:
```
$twigEnv = new Twig_Environment($loader, $options);

$twigEnv->addExtension(new AppNeta\TraceViewTwig\Extension\TraceViewRUMExtension);
```

For full details on placing these snippets, [please see this knowledge base article](https://support.tv.appneta.com/support/solutions/articles/86401-php-rum). Here
is a basic example:
```
<html>
  <head>
    <meta ... >
    {{ oboe_get_rum_header() }}
  </head>

  <body>
    ...
    {{ oboe_get_rum_footer() }}
  </body>
</html>
```
*Note: these functions only accept one argument: whether to output `<script>`
tags around the snippet (defaults to `true`).*

The best place to put these functions is in your root Twig layout template (such
as `TwigBundle::layout.html.twig`). It is possible to use these Twig functions
in a block, like `javascript` or `body`, but there are two downsides: you may
inadvertantly output the script tags more than once per HTML document, and the
timing information will be less accurate if the JavaScript gets loaded after other JavaScript or CSS stylesheets.

# Contributors

Thanks to Willem van der Jagt of Cakemail for writing the initial integration
between TraceView RUM and Twig!

# Contributing

The best way to improve this package is to work with the people using it! We
actively encourage patches, pull requests, feature requests, and bug reports.
