<?php

namespace Backpack\CRUD\Tests\Unit\CrudPanel;

use Backpack\CRUD\Tests\config\CrudPanel\BaseCrudPanel;

/**
 * @covers Backpack\CRUD\app\Library\CrudPanel\Traits\Macroable
 */
class CrudPanelMacroTest extends BaseCrudPanel
{
    public function testItCanRegisterMacro()
    {
        $this->crudPanel::macro('validMacro', fn () => true);

        $this->assertTrue($this->crudPanel->validMacro());
    }

    public function testThrowsErrorIfMacroExists()
    {
        $e = null;
        try {
            $this->crudPanel::macro('setModel', fn () => true);
        } catch (\Throwable $e) {
        }
        $this->assertEquals(
            new \Symfony\Component\HttpKernel\Exception\HttpException(500, 'Cannot register \'setModel\' macro. \'setModel()\' already exists on Backpack\CRUD\app\Library\CrudPanel\CrudPanel'),
            $e
        );
    }
}
