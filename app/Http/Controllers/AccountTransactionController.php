<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionEntryResource;
use App\Models\Account;
use App\Models\TransactionEntry;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class AccountTransactionController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Account $account, Request $request)
    {
        $this->authorize('view', $account);

        $model = app(TransactionEntry::class);

        $filters = [];

        foreach ($model->queryBuilderFilterFields() as $key) {
            $filters[] = $key;
        }

        $transactions = QueryBuilder::for(TransactionEntry::class)
            ->with('bank')
            ->performedBy($account->id)
            ->notFailed()
            ->defaultSort('-created_at')
            ->allowedSorts($model->queryBuilderSortFields())
            ->allowedFilters($filters)
            ->jsonPaginate()
            ->appends($request->all());

        return $this->respond([
            'transaction_entries' => TransactionEntryResource::collection($transactions)->response()->getData(true),
        ]);

    }
}
