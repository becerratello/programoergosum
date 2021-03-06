<?php

namespace App\Admin;

use App\Entity\Category;
use App\Entity\Course;
use App\Entity\Lesson;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Sonata\CoreBundle\Form\Type\CollectionType;

class LessonAdmin extends AbstractAdmin
{
    protected $datagridValues = [
        '_page' => 1,
        '_sort_order' => 'ASC',
        '_sort_by' => 'course',
    ];

    public function toString($object)
    {
        return $object instanceof Lesson ? $object->getTitle() : 'Lección';
    }   

    public function getBatchActions()
    {
        $actions = parent::getBatchActions();
        unset($actions['delete']);
        return $actions;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['list', 'show', 'create', 'edit']);
        $collection->add('move', $this->getRouterIdParameter().'/move/{position}');
    }



    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('course', null, [], EntityType::class, ['class' => Course::class, 'choice_label' => 'title' ],
                [ 'show_filter' => true, 'label' => 'Curso' ]
            )
            ->add('title', null, 
                [ 'show_filter' => true, 'label' => 'Título' ]
            )
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id', null, 
                [
                    'label' => '#id',
                    'header_style' => 'width: 50px; text-align: center;',
                    'row_align' => 'center'
                ]
            )
            ->add('title', null, 
                [
                    'label' => 'Título',
                ]
            )
            ->add('alias', null, 
                [
                    'label' => 'Alias',
                    'header_style' => 'width: 200px;',
                ]
            )
            /*
            ->add('getLessons', 'integer', 
                [
                    'label' => 'Lecciones',
                    'header_style' => 'width: 100px;',
                ]
            )
            */
            ->add('course.title', null, 
                [
                    'label' => 'Curso',
                    'header_style' => 'width: 150px;',
                ]
            )
            ->add('active', 'boolean', 
                [
                    //'editable' => true, 
                    'label' => 'Estado',
                    'header_style' => 'width: 100px; text-align: center',
                    'row_align' => 'center'
                ]
            )
            ->add('access', 'choice', 
                [
                    'choices' => [
                        '1' => 'USER',
                        '2' => 'PREMIUM',
                    ],
                    'label' => 'Acceso',
                    'header_style' => 'width: 80px; text-align: center',
                    'row_align' => 'center'
                ]
            )
            /*
            ->add('cdate', 'datetime', 
                [
                    'format' => 'd-m-Y H:i',
                    'label' => 'Creado',
                    'header_style' => 'width: 140px; text-align: center',
                    'row_align' => 'center'
                ]
            )
            */
            ->add('mdate', 'datetime', 
                [
                    'format' => 'd-m-Y H:i',
                    'label' => 'Modificado',
                    'header_style' => 'width: 140px; text-align: center',
                    'row_align' => 'center'
                ]
            )
            ->add('position', null, 
                [
                    'label' => 'Posición',
                    'header_style' => 'width: 80px; text-align: center',
                    'row_align' => 'center'
                ]
            )
            ->add('_ordering', 'actions', 
                [
                    'actions' => 
                    [
                        'move' => [
                            'template' => '@PixSortableBehavior/Default/_sort_drag_drop.html.twig',
                            'enable_top_bottom_buttons' => false
                        ],
                    ],
                    'header_style' => 'width: 150px; text-align: center',
                    'row_align' => 'center'
                ]
            )
            ->add('_action', 'actions', 
                [
                    'actions' => 
                    [
                        'show' => [],
                        'edit' => [],
                    ],
                    'header_style' => 'width: 180px; text-align: center',
                    'row_align' => 'center'
                ]
            )
        ;
    }



    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('General', ['class' => 'col-md-8'])
                ->add('title', null, 
                    [
                        'label' => 'Título',
                    ]
                )
                ->add('alias', null, 
                    [
                        'label' => 'Alias',
                    ]
                )
                ->add('introtext', null, 
                    [
                        'label' => 'Descripción',
                    ]
                )
            ->end()
        ;

        $showMapper
            ->with('Configuración', ['class' => 'col-md-4'])
                ->add('active', 'boolean', 
                    [
                        'label' => 'Habilitado',
                    ]
                )
                ->add('cdate', 'datetime', 
                    [
                        'format' => 'd-m-Y H:i',
                        'label' => 'Creado',
                    ]
                )
                ->add('mdate', 'datetime', 
                    [
                        'format' => 'd-m-Y H:i',
                        'label' => 'Modificado',
                    ]
                )
            ->end()
        ;     
    }



    protected function configureFormFields(FormMapper $formMapper)
    {
        $object = $this->getSubject();
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $er = $em->getRepository($this->getClass());

        $formMapper
            ->with('General', ['class' => 'col-md-8']) 
                ->add('title', TextType::class, 
                    [
                        'label'=>'Título'
                    ]
                )
                ->add('alias', TextType::class, 
                    [
                        'label'=>'Alias', 
                    ]
                )
                ->add('video', TextType::class, 
                    [
                        'label'=>'Video', 
                    ]
                )
                ->add('description', TextareaType::class, 
                    [
                        'label'=>'Descripción', 
                    ]
                )
            ->end()
        ;

        $formMapper
            ->with('Configuración', ['class' => 'col-md-4'])
                /*
                ->add('position', TextType::class, 
                    [
                        'label' => 'Posición',
                        'disabled' => true
                    ]
                )
                */
                ->add('active', ChoiceType::class, 
                    [
                        'choices' => [
                            'Activado' => true,
                            'Desactivado' => false,
                        ],
                        'label' => 'Estado'
                    ]
                )
                ->add('score', TextType::class, 
                    [
                        'label' => 'Puntos'
                    ]
                )
            ->end()
        ;

    }

}