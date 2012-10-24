#How to configure

1. Add form template

<pre>
#app/config/config.yml
twig:
    # ...
    form:
        resources:
            - 'StpRedactorBundle:Redactor:fields.html.twig'
</pre>

2. Configure Redactor views:

Run app/console config:dump-reference stp_redactor for check documenttion:

Example:

<pre>
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
    algorithm:
        upload_image:
            dir: "%kernel.root_dir%/../web/uploads/content/images"
        role: [IS_AUTHENTICATED_FULLY]
    comments:
        role: [ROLE_ADMIN, IS_AUTHENTICATED_ANONYMOUSLY]
        settings:
            lang: en
</pre>

#How to use

## Use in form type

<pre>
//page_with_redactor.html.twig
{% block javascripts %}
    {{ parent() }}
    &lt;script type="text/javascript" src="{{ asset('bundles/pathtojquery/js/jquery.js') }}"></script>
    &lt;script type="text/javascript" src="{{ asset('bundles/stpredactor/js/redactor.js') }}"></script>
    &lt;script type="text/javascript" src="{{ asset('bundles/stpredactor/js/script.js') }}"></script>
{% endblock %}

{% block stylesheets %}
    {{ parent() }}
    &lt;link rel="stylesheet" href="{{ asset('bundles/stpredactor/css/redactor.css') }}" type="text/css" media="screen" />
{% endblock %}
</pre>

<pre>
&lt;?php
//RedactorPageType.php
namespace Stp\SomeBundle\Form;

use Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\Form\AbstractType;

class RedactorPageType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('description', 'redactor', array('redactor' => 'admin'));
    }
}
</pre>

## Use in SonataAdminBundle
Add bundles/stpredactor/css/sonata.css file to sonata layout for avoid some markup issue


