<?php
/**
 * Created by PhpStorm.
 * User: victor
 * Date: 10.01.16
 * Time: 12:49
 */

namespace AppBundle\Tests\Form\Type;

use AppBundle\Form\Type\CommentType;
use Symfony\Component\Form\Test\TypeTestCase;

class CommentTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = array(
            'rating' => '5',
            'text' => 'test text',
        );

        $form = $this->factory->create(CommentType::class);

//        $object = TestObject::fromArray($formData);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
//        $this->assertEquals($object, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}