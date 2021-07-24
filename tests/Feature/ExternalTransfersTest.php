<?php

namespace Tests\Feature;

use App\DataTransferObjects\PaystackTransferObject;
use App\DataTransferObjects\PaystackTransferRecipientObject;
use App\Models\Account;
use App\Models\User;
use App\Options\TransactionEntryTypes;
use App\Services\PaystackClient;
use Database\Seeders\AccountSeeder;
use Database\Seeders\BankSeeder;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ExternalTransfersTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * @var Account
     */
    private $debitAccount;

    /**
     * @var User
     */
    private $user;

    /**
     * @var Account
     */
    private $nubanAccount;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutExceptionHandling();

        $this->seed(BankSeeder::class);

        $this->debitAccount = Account::factory()->create([
            'book_balance' => '4000000',
            'available_balance' => '4000000'
        ]);

        $this->nubanAccount = Account::factory()->create([
            'number' => config('accounts.nuban_settlement_account'),
        ]);

        $this->user = $this->debitAccount->owner;
    }

    /** @test */
    public function users_can_make_money_transfers_to_external_banks_from_accounts_they_own()
    {
        $recipientResponse = $this->createHttpClientResponse('tests/stubs/transfer_recipient.json');

        $transferResponse = $this->createHttpClientResponse('tests/stubs/transfer.json');

        $this->mock(PaystackClient::class)
            ->shouldReceive('createTransferRecipient')
            ->andReturn(PaystackTransferRecipientObject::create($recipientResponse->json()['data']))
            ->shouldReceive('transfer')
            ->andReturn(PaystackTransferObject::create($transferResponse->json()['data']));

        // given there is an account with sufficient available balance
        $form = [
            'debit_account_number' => $this->debitAccount->number,
            'amount' => '1,000.40',
            'credit_account_number' => $this->faker->regexify('[0-9]{10}'),
            'credit_account_name' => $this->faker->name,
            'credit_bank_code' => '044',
            'description' => $this->faker->words(5, true),
            'reference' => Str::uuid()->getHex()->toString(),
            'pin' => '1111',
        ];

        // when the user submits a form with the required data to an endpoint
        Sanctum::actingAs($this->user);

        $balance = $this->debitAccount->available_balance - convertAmountToBaseUnit($form['amount']);

        $this->postJson(route('api.transactions.store'), $form)
            ->assertCreated();

        $transactionAmount = convertAmountToBaseUnit($form['amount']);

        // a transaction entry happens on that account
        $this->assertDatabaseHas('transaction_entries', [
            'debit_account_id' => $this->debitAccount->id,
            'external_account_name' => $recipientResponse->json()['data']['details']['account_name'],
            'external_account_number' => $recipientResponse->json()['data']['details']['account_number'],
            'external_bank_code' => $recipientResponse->json()['data']['details']['bank_code'],
            'status' => $transferResponse->json()['data']['status'],
            'description' => $form['description'],
            'amount' => -$transactionAmount,
            'reference' => $form['reference'],
            'type' => TransactionEntryTypes::DEBIT
        ]);

        $this->assertDatabaseHas('transaction_entries', [
            'credit_account_id' => $this->nubanAccount->id,
            'external_account_name' => $recipientResponse->json()['data']['details']['account_name'],
            'external_account_number' => $recipientResponse->json()['data']['details']['account_number'],
            'external_bank_code' => $recipientResponse->json()['data']['details']['bank_code'],
            'status' => $transferResponse->json()['data']['status'],
            'description' => $form['description'],
            'amount' => $transactionAmount,
            'type' => TransactionEntryTypes::CREDIT
        ]);

        $this->assertEquals($balance, $this->debitAccount->fresh()->available_balance);

        $this->assertDatabaseCount('transactions', 1);
    }

}
