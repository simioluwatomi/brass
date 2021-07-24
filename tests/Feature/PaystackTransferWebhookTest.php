<?php

namespace Tests\Feature;

use App\DataTransferObjects\PaystackTransferObject;
use App\Models\TransactionEntry;
use App\Options\PaystackOptions;
use App\Options\TransactionEntryStatus;
use App\Options\TransactionEntryTypes;
use App\Services\PaystackClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PaystackTransferWebhookTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var TransactionEntry
     */
    private $debit;
    private $credit;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutExceptionHandling();

        $this->debit = TransactionEntry::factory()
            ->externalDebit()
            ->create(['status' => TransactionEntryStatus::PENDING]);

        $this->credit = TransactionEntry::factory()
            ->externalCredit()
            ->create([
                'status' => $this->debit->status,
                'amount' => abs($this->debit->amount)
            ]);

        $this->debit->credit()->sync($this->credit->id);
    }

    /** @test  */
    public function it_processes_webhooks_for_transfer_notifications()
    {
        $jsonResponse = $this->createHttpClientResponse('tests/stubs/fetched_transfer.json');

        $transferObject = PaystackTransferObject::create($jsonResponse->json()['data']);

        // update the reference to match that of the debit transaction
        $transferObject->reference = $this->debit->reference;

        $this->mock(PaystackClient::class)
            ->shouldReceive('getTransfer')
            ->andReturn($transferObject);

        $webhookStubs = [
            'transfer_failed_webhook.json',
            'transfer_reversed_webhook.json',
            'transfer_successful_webhook.json'
        ];

        $stub = $webhookStubs[array_rand($webhookStubs)];

        $form = json_decode(file_get_contents(base_path("tests/stubs/$stub")), true);

        $form['data']['transfer_code'] = $this->debit->reference;

        $this->withHeaders([
            PaystackOptions::WEBHOOK_HEADER => hash_hmac('sha512', json_encode($form), config('services.paystack.secret_key'))
        ])
            ->postJson(route('webhooks.paystack.transfer'), $form)
            ->assertOk();

        $this->assertDatabaseHas('transaction_entries', [
            'id' => $this->debit->id,
            'status' => $transferObject->status,
        ]);

        $this->assertDatabaseHas('transaction_entries', [
            'id' => $this->credit->id,
            'status' => $transferObject->status,
        ]);

        if ($transferObject->status == TransactionEntryStatus::SUCCESS) {
           $this->assertEquals($this->debit->debitAccount->book_balance, $this->debit->debitAccount->available_balance);
        } else {
            $this->assertEquals($this->debit->debitAccount->available_balance, $this->debit->debitAccount->book_balance);
        }

        $this->assertDatabaseCount('webhooks', 1);
    }
}
