<?php

/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticBadgeGeneratorBundle\Form\Type;

use Mautic\CoreBundle\Helper\ArrayHelper;
use Mautic\LeadBundle\Model\FieldModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\File;

class BadgeTextType extends AbstractType
{
    /**
     * @var FieldModel
     */
    private $fieldModel;

    /**
     * BadgeTextType constructor.
     *
     * @param FieldModel $fieldModel
     */
    public function __construct(FieldModel $fieldModel)
    {

        $this->fieldModel = $fieldModel;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $formModifier = function (FormInterface $form, $currentColumns) {
            $order = [];
            $orderColumns = [];
            if (!empty($currentColumns)) {
                $orderColumns = array_values($currentColumns);
                $order        = htmlspecialchars(json_encode($orderColumns), ENT_QUOTES, 'UTF-8');
            }
            $form->add(
                'fields',
                ChoiceType::class,
                [
                    'label'      => 'mautic.plugin.badge.generator.form.fields',
                    'label_attr' => ['class' => 'control-label'],
                    'choices'=> $this->fieldModel->getFieldList(false),
                    'attr'       => [
                        'class' => 'form-control multiselect',
                        'data-sortable' => 'true',
                        'data-order'    => $order,
                    ],
                    'required'   => false,
                    'multiple'   => true,
                    'expanded'    => false,
                    'data' => $orderColumns
                ]
            );
        };
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                $data = $event->getData();
                $columns = isset($data['fields']) ? $data['fields'] : [];
                $formModifier($event->getForm(), $columns);
            }
        );
        // Build the columns selector
        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $data    = $event->getData();
                $columns = isset($data['fields']) ? $data['fields'] : [];

                $formModifier($event->getForm(), $columns);
            }
        );

        $builder->add(
            'color',
            'text',
            [
                'label'      => 'mautic.focus.form.text_color',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'       => 'form-control',
                    'data-toggle' => 'color',
                ],
                'required'   => false,
            ]
        );

        $coreFonts = [
            "times",
            "symbol",
            "timesb",
            "timesi",
            "aefurat",
            "courier",
            "timesbi",
            "courierb",
            "courieri",
            "freemono",
            "freesans",
            "courierbi",
            "freemonob",
            "freemonoi",
            "freesansb",
            "freesansi",
            "freeserif",
            "helvetica",
            "pdfatimes",
            "dejavusans",
            "freemonobi",
            "freesansbi",
            "freeserifb",
            "freeserifi",
            "helveticab",
            "helveticai",
            "pdfasymbol",
            "pdfatimesb",
            "pdfatimesi",
            "freeserifbi",
            "aealarabiya",
            "dejavusansb",
            "dejavusansi",
            "dejavuserif",
            "helveticabi",
            "pdfacourier",
            "pdfatimesbi",
            "dejavusansbi",
            "dejavuserifb",
            "dejavuserifi",
            "pdfacourierb",
            "pdfacourieri",
            "custom",
        ];


        $builder->add(
            'font',
            'choice',
            [
                'choices'     => array_combine($coreFonts, $coreFonts),
                'label'       => 'mautic.plugin.badge.generator.form.font',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => [
                    'class' => 'form-control',
                ],
                'empty_value' => '',
                'required'    => false,
            ]
        );


        $builder->add(
            'ttf',
            'file',
            [
                'label'      => 'mautic.plugin.badge.generator.form.font.upload',
                'label_attr' => ['class' => 'control-label'],
                'required'   => false,
                'attr'       => [
                    'class'   => 'form-control',
                    'data-show-on' => '{"badge_properties_text'.ArrayHelper::getValue('index', $options['data'], '1').'_font":"custom"}',

                ],
                'mapped'      => false,
                'constraints' => [
                    new File(
                        [
                            'mimeTypes' => [
                                'font/ttf',
                            ],
                            'mimeTypesMessage' => 'mautic.plugin.badge.generator.form.font.ttf_invalid',
                        ]
                    ),
                ],
            ]
        );


        $builder->add(
            'align',
            'choice',
            [
                'choices'     => [
                    'C' => 'mautic.core.center',
                    'L' => 'mautic.core.left',
                ],
                'label'       => 'mautic.plugin.badge.generator.form.text.align',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => [
                    'class' => 'form-control',
                ],
                'required'    => false,
                'data'  => isset($options['data']['align']) ? $options['data']['align'] : 'L',
                'empty_value' => false,
            ]
        );

        $builder->add(
            'position',
            TextType::class,
            [
                'label'      => 'mautic.plugin.badge.generator.form.text.position.y',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class' => 'form-control',
                ],
                'data'  => isset($options['data']['position']) ? $options['data']['position'] : 0,
                'required'   => false,
            ]
        );

        $builder->add(
            'positionX',
            TextType::class,
            [
                'label'      => 'mautic.plugin.badge.generator.form.text.position.x',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class' => 'form-control',
                ],
                'data'  => isset($options['data']['positionX']) ? $options['data']['positionX'] : 0,
                'required'   => false,
            ]
        );

        $builder->add(
            'fontSize',
            TextType::class,
            [
                'label'      => 'mautic.plugin.badge.generator.form.font.size',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class' => 'form-control',
                ],
                'data'  => isset($options['data']['fontSize']) ? $options['data']['fontSize'] : 30,
                'required'   => false,
            ]
        );

        $builder->add(
            'stretch',
            NumberType::class,
            [
                'label'      => 'mautic.plugin.badge.generator.form.text.stretch',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class' => 'form-control',
                ],
                'data'  => isset($options['data']['stretch']) ? $options['data']['stretch'] : 0,
                'required'   => false,
            ]
        );

        $builder->add(
            'style',
            'choice',
            [
                'choices'     => [
                    'B' => 'mautic.plugin.badge.generator.form.text.bold',
                    'I' => 'mautic.plugin.badge.generator.form.text.italic',
                    'U' => 'mautic.plugin.badge.generator.form.text.underline',
                ],
                'label'       => 'mautic.plugin.badge.generator.form.text.style',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => [
                    'class' => 'form-control',
                ],
                'required'    => false,
                'data'  => isset($options['data']['style']) ? $options['data']['style'] : [],
                'multiple' => true,
            ]
        );
    }
}
