<?php

use Illuminate\Support\Facades\File;
use Mtolhuys\LaravelSchematics\Actions\Model\CreateModelAction;
use Mtolhuys\LaravelSchematics\Actions\Model\DeleteModelAction;
use Mtolhuys\LaravelSchematics\Actions\Model\EditModelAction;
use Mtolhuys\LaravelSchematics\Tests\TestCase;

class WriteModelsTest extends TestCase
{
    private $fields = [
        [
            'id' => '587a0014-6e83-482d-a1ac-7b25b0e4dc10',
            'exists' => 'false',
            'error' => 'false',
            'changed' => 'false',
            'name' => 'id',
            'type' => 'increments',
            'columnType' => null
        ],
        [
            'id' => '587a0922-6e83-482d-a1ac-7b25b0e4dc10',
            'exists' => 'false',
            'error' => 'false',
            'changed' => 'false',
            'name' => 'name',
            'type' => null,
            'columnType' => null
        ]
    ];

    private $path;

    public function setUp(): void
    {
        parent::setUp();

        $this->path = app_path(str_replace(['App\\', '\\'], ['', '/'], $this->modelNamespace) . 'Acme.php');
    }

    /** @test */
    public function it_successfully_creates_models()
    {
        (new CreateModelAction())->execute([
            'name' => 'Acme',
            'fields' => $this->fields,
        ]);

        $this->assertTrue(File::exists($this->path));

        $content = File::get($this->path);

        $this->assertStringContainsString(rtrim($this->modelNamespace, '\\'), $content);
        $this->assertStringContainsString('Acme', $content);
        $this->assertStringContainsString('protected $fillable', $content);
        $this->assertStringContainsString("'id',", $content);
        $this->assertStringContainsString("'name',", $content);
    }

    /** @test */
    public function it_successfully_edits_models()
    {
        (new EditModelAction())->execute([
            'model' => "{$this->modelNamespace}Acme",
            'fields' => array_merge($this->fields, [
                [
                    'id' => '902a0922-6e83-482d-k0px-7b25b0e4dc10',
                    'exists' => 'false',
                    'error' => 'false',
                    'changed' => 'false',
                    'name' => 'email',
                    'type' => null,
                    'columnType' => null
                ]
            ]),
        ]);

        $this->assertTrue(File::exists($this->path));

        $content = File::get($this->path);

        $this->assertStringContainsString(rtrim($this->modelNamespace, '\\'), $content);
        $this->assertStringContainsString('Acme', $content);
        $this->assertStringContainsString('protected $fillable', $content);
        $this->assertStringContainsString("'id',", $content);
        $this->assertStringContainsString("'name',", $content);
        $this->assertStringContainsString("'email',", $content);
    }

    /** @test */
    public function it_successfully_deletes_models()
    {
        (new DeleteModelAction())->execute([
            'name' => "{$this->modelNamespace}Acme",
        ]);

        $this->assertFalse(File::exists($this->path));
    }
}
