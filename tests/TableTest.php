<?php

//TODO setData should accept a collection instead of an array of arrays

use Chumper\Datatable\Table;

class TableTest extends PHPUnit_Framework_TestCase {

    /**
     * @var \Mockery\Mock
     */
    private $view;

    /**
     * @var Table
     */
    private $table;

    protected function setUp()
    {
        parent::setUp();
        $this->table = new Table();

        //View Mock
        $app = Mockery::mock('AppMock');
        $app->shouldReceive('instance')->once()->andReturn($app);

        Illuminate\Support\Facades\Facade::setFacadeApplication($app);

        $this->view = Mockery::mock('ViewMock');
        Illuminate\Support\Facades\View::swap($this->view);
    }

    /**
     * @expectedException Exception
     */
    public function testSetOptions()
    {
        $this->table->setOptions('foo','bar');

        $this->table->setOptions(array(
            'foo2' => 'bar2',
            'foo3' => 'bar3'
        ));

        $this->table->setOptions('foo', 'bar', 'baz');
    }

    /**
     * @expectedException Exception
     */
    public function testSetCallbacks()
    {
        $this->table->setCallbacks('foo', 'bar');
        $this->assertArrayHasKey('foo', $this->table->getCallbacks());

        $this->table->setCallbacks(array(
            'foo2' => 'bar2',
            'foo3' => 'bar3'
        ));
        $this->assertArrayHasKey('foo2', $this->table->getCallbacks());
        $this->assertArrayHasKey('foo3', $this->table->getCallbacks());

        $this->table->setCallbacks('foo', 'bar', 'baz');
        $this->assertTrue(False);  // should throw exception before here
    }

    /**
     * @expectedException Exception
     */
    public function testSetCustomValues()
    {
        $this->table->setCustomValues('foo', 'bar');
        $this->assertArrayHasKey('foo', $this->table->getCustomValues());

        $this->table->setCustomValues(array(
            'foo2' => 'bar2',
            'foo3' => 'bar3'
        ));
        $this->assertArrayHasKey('foo2', $this->table->getCustomValues());
        $this->assertArrayHasKey('foo3', $this->table->getCustomValues());

        $this->table->setCustomValues('foo', 'bar', 'baz');
        $this->assertTrue(False);  // should throw exception before here
    }

    public function testAddColumn()
    {
        $this->table->addColumn('foo');

        $this->assertEquals(1, $this->table->countColumns());

        $this->table->addColumn('foo1','foo2');

        $this->assertEquals(3, $this->table->countColumns());

        $this->table->addColumn(array('foo3','foo4'));

        $this->assertEquals(5, $this->table->countColumns());
    }

    public function testRender()
    {
        $this->view->shouldReceive('make')->once()
            ->with('datatable::template', array(
                'options'   => array(),
                'callbacks' => array(),
                'values'    => array(),
                'data'      => array(),
                'columns'   => array(
                    'foo'
                ),

            ))->andReturn(true);

        $table1 = $this->table->addColumn('foo')->render();

        $this->assertTrue($table1);

    }

    public function testSetData()
    {
        $data = array(
            array(
                'foo',
                'bar'
            ),
            array(
                'foo2',
                'bar2'
            ),
        );

        $this->table->setData($data);
        $this->assertEquals($data,$this->table->getData());

    }

    public function testSetUrl()
    {
        $this->table->setUrl('foo/url');

        $this->assertArrayHasKey('bServerSide',$this->table->getOptions());
        $this->assertArrayHasKey('sAjaxSource',$this->table->getOptions());

        $return = $this->table->getOptions();

        $this->assertEquals('foo/url',$return['sAjaxSource']);
    }

    protected function tearDown()
    {
        Mockery::close();
    }
}