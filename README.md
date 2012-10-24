# RedactorBundle

Bundle is destined to add Redactor WYSIWYG editor to your Symfony2 project.

This bundle does not include original Redactor JavaScript library, for using it you should get it from official website http://imperavi.com/redactor/download/

## Installation for Symfony 2.1

### composer.json
```js
"require": {
    ...
    "stp/redactor-bundle": "dev-master"
    ...
},
"repositories": [
    {
        "url": "https://github.com/AStepanov/RedactorBundle.git",
        "type": "vcs"
    }
],
```

### app/AppKernel.php

```php
<?php
public function registerBundles()
{
    $bundles = array(
        // ...
        new Stp\RedactorBundle\StpRedactorBundle(),
    );
}
```

run the command

```bash
php app/console assets:install web
```

### app/config/confug.yml

```yml
# Twig Configuration
twig:
    # ...
    form:
        resources:
            - 'StpRedactorBundle:Redactor:fields.html.twig'
```

### To allow uploading files add the following lines to app/config/routing.yml

```yml
stp_redactor:
    resource: "@StpRedactorBundle/Controller/"
    type:     annotation
    prefix:   /redactor/
```


## How to configure

RedactorBundle provide opportunity to configure some different options of using (e.g. admin, comments, blog) 

Run the command for check all config options

```bash
php app/console config:dump-reference stp_redactor
```

### Example:

```yml
#app/config/config.yml
stp_redactor:
    admin:
        upload_file:
            dir: "%kernel.root_dir%/../web/uploads/content/files"
            maxSize: 10M
            mimeTypes:
                - image/png
                - image/jpeg
        upload_image:
            dir: "%kernel.root_dir%/../web/uploads/content/images"
            maxSize: 5M
            minWidth: 100
            maxWidth: 900
            minHeight: 300
            maxHeight: 900
        role: [ROLE_ADMIN]
    blog:
        upload_image:
            dir: "%kernel.root_dir%/../web/uploads/blog/images"
        role: [IS_AUTHENTICATED_FULLY]
    comments:
        role: [IS_AUTHENTICATED_ANONYMOUSLY]
        settings:
            lang: en
```

## How to use

### Use in Form Type

```twig
{# template_with_redactor.html.twig #}

{% block javascripts %}
    {{ parent() }}
    <script type="text/javascript" src="/path_to_jquery/jquery.js"></script>
    <script type="text/javascript" src="/path_to_original_redactor/js/redactor.js"></script>
    <script type="text/javascript" src="{{ asset('bundles/stpredactor/js/script.js') }}"></script>
{% endblock %}

{% block stylesheets %}
    {{ parent() }}
    <link rel="stylesheet" href="/path_to_original_redactor/css/redactor.css" type="text/css" media="screen" />
{% endblock %}
```

```php
<?php
//BlogPostType.php
namespace Stp\BlogBundle\Form;

use Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\Form\AbstractType;

class BlogPostType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('description', 'redactor', array('redactor' => 'blog'));
    }
}
```

### Use in SonataAdminBundle

#### Create new SonataAdmin layout 

```twig
{# app/Resources/views/admin_layout.html.twig #}

{% extends 'SonataAdminBundle::standard_layout.html.twig' %}

{% block stylesheets %}
    {{ parent() }}
    <link rel="stylesheet" href="/path_to_original_redactor/css/redactor.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="{{ asset('bundles/stpredactor/css/sonata.css') }}" type="text/css" media="screen" />
{% endblock %}

{% block javascripts %}
    {{ parent() }}
    <script type="text/javascript" src="/path_to_original_redactor/js/redactor.js"></script>
    <script type="text/javascript" src="{{ asset('bundles/stpredactor/js/script.js') }}"></script>
{% endblock %}
```

#### Set path to you layout

```yml
# app/config/config.yml
sonata_admin:
    templates:
        layout: ::admin_layout.html.twig
```