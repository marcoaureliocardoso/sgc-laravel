<?php

namespace Tests\Feature;

use App\Events\ModelListed;
use App\Models\Bond;
use App\Models\BondDocument;
use App\Models\Document;
use App\Models\DocumentType;
use App\Repositories\BondDocumentRepository;
use App\Repositories\RightsDocumentRepository;
use App\Services\BondDocumentService;
use App\Services\Dto\StoreDocumentDto;
use App\Services\RightsDocumentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class BondDocumentServiceTest extends TestCase
{
    use RefreshDatabase;

    private BondDocumentService $service;
    private RightsDocumentService $rightsService;

    /** @return void  */
    public function __construct()
    {
        parent::__construct();

        $this->service = new BondDocumentService(new BondDocumentRepository());
        $this->rightsService = new RightsDocumentService(new RightsDocumentRepository());
    }


    //setting up scenario for all tests
    public function setUp(): void
    {
        parent::setUp();

        Document::factory()->createOne(
            [
                'original_name' => 'Document Alpha.pdf',
                'documentable_id' => BondDocument::factory()
                    ->createOne([
                        'bond_id' => Bond::factory()->createOne()->id,
                    ])->id,
                'documentable_type' => BondDocument::class,
                'document_type_id' => DocumentType::factory()
                    ->createOne(
                        [
                            'name' => 'Type One',
                        ]
                    )->id,
            ]
        );

        Document::factory()->createOne(
            [
                'original_name' => 'Document Beta.pdf',
                'documentable_id' => BondDocument::factory()
                    ->createOne([
                        'bond_id' => Bond::factory()->createOne()->id,
                    ])->id,
                'documentable_type' => BondDocument::class,
                'document_type_id' => DocumentType::factory()
                    ->createOne(
                        [
                            'name' => 'Type Two',
                        ]
                    )->id,
            ]
        );
    }

    /**
     * @test
     */
    public function documentsShouldBeListed(): void
    {
        Event::fakeFor(function () {
            //execution
            $documents = $this->service->list();

            //verifications
            Event::assertDispatched(ModelListed::class);
            $this->assertCount(2, $documents);
            $this->assertContains('Document Alpha.pdf', $documents->pluck('original_name')->toArray());
            $this->assertContains('Document Beta.pdf', $documents->pluck('original_name')->toArray());
        });
    }

    /**
     * @test
     */
    public function rightsShouldBeListed(): void
    {
        Document::factory()->createOne(
            [
                'original_name' => 'Document Rights.pdf',
                'documentable_id' => BondDocument::factory()
                    ->createOne([
                        'bond_id' => Bond::factory()
                            ->createOne(
                                [
                                    'uaba_checked_at' => now(),
                                    'impediment' => false,
                                ]
                            )->id,
                    ])->id,
                'documentable_type' => BondDocument::class,
                'document_type_id' => DocumentType::factory()
                    ->createOne(
                        [
                            'name' => 'Ficha de Inscrição - Termos e Licença',
                        ]
                    )->id,
            ]
        );

        Event::fakeFor(function () {
            //execution
            $documents = $this->rightsService->list();

            //verifications
            Event::assertDispatched(ModelListed::class);
            $this->assertCount(1, $documents);
            $this->assertContains('Document Rights.pdf', Document::whereHasMorph('documentable', BondDocument::class)->pluck('original_name')->toArray());
        });
    }

    /**
     * @test
     */
    public function documentShouldBeCreated(): void
    {
        //setting up scenario
        $attributes['fileName'] = (string) 'Document Gama.pdf';
        $attributes['fileData'] = (string) 'data:application/pdf;base64,';
        $attributes['documentTypeId'] = (string) 1;
        $attributes['referentId'] = (string) 1;

        $dto = new StoreDocumentDto($attributes);

        Event::fakeFor(function () use ($dto) {
            //execution
            $this->service->create($dto);

            //verifications
            Event::assertDispatched('eloquent.created: ' . Document::class);
            $this->assertContains('Document Gama.pdf', Document::whereHasMorph('documentable', BondDocument::class)->pluck('original_name')->toArray());
            $this->assertCount(3, Document::whereHasMorph('documentable', BondDocument::class)->get());
            $this->assertCount(3, BondDocument::all());
        });
    }
}
