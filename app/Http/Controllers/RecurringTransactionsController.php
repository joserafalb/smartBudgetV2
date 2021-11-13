<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Category;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\RecurringTransactions;

class RecurringTransactionsController extends Controller
{
    const LIST_VIEW = 'RecurringTransaction/List';
    const EDIT_VIEW = 'RecurringTransaction/Edit';

    const VALIDATIONS = [
        'item' => 'array',
        'item.bankAccountId' => 'required',
        'item.categoryId' => 'required',
        'item.amount' => 'required',
        'item.startDate' => 'required',
        'item.description' => 'required',
        'item.scheduleType' => 'required',
        'item.scheduleParameter' => 'required',
        'item.limitType' => 'required',
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = RecurringTransactions::select('id', 'name', 'start_date', 'end_date', 'active');

        if ($request->search) {
            $data->where('name', 'like', "%$request->search%");
        }

        if ($request->sortBy) {
            $data->orderBy($request->sortBy, $request->sortDesc ? 'DESC' : 'ASC');
        }

        $data = $data->paginate(12);

        return Inertia::render(self::LIST_VIEW, [
            'data' => $data,
            'options' => [
                'page' => $data->currentPage(),
                'search' => $request->search ?? '',
                'sortBy' => $request->sortBy ?? '',
                'sortDesc' => $request->sortDesc,
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render(self::EDIT_VIEW, [
            'bankAccounts' => BankAccount::orderBy('name')->select('id', 'name')->get(),
            'categories' => Category::orderBy('name')->select('id', 'name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate(array_merge(
            ['item.name' => 'required|unique:bank_accounts,name'],
            self::VALIDATIONS
        ));

        $data = [
            'name' => $request->item['name'],
            'bank_account_id' => $request->item['bankAccountId'],
            'category_id' => $request->item['categoryId'],
            'amount' => $request->item['amount'],
            'start_date' => $request->item['startDate'],
            'description' => $request->item['description'],
            'schedule_type' => $request->item['scheduleType'],
            'schedule_parameter' => $request->item['scheduleParameter'],
            'limit_type' => $request->item['limitType'],
            'active' => $request->item['active'] ?? false,
        ];

        if (!empty($request->item['quantity'])) {
            $data['quantity'] = $request->item['quantity'];
            $data['end_date'] =  $this->getEndDate($request->item);
        } else if (!empty($request->item['end_date'])) {
            $data['end_date'] = $request->item['endDate'];
        }

        $newRow = RecurringTransactions::create($data);
        return response()->json($newRow->id);
    }

    private function getEndDate($data)
    {
        if ($data['limitType'] === 'Number of transactions') {
            // Calculate end_date
            $scheduleType = $data['scheduleType'];
            $transactionsQuantity = $data['quantity'];
            $startDate = $data['startDate'];
            $scheduleParameter = $data['scheduleParameter'];

            $startDateObject = \DateTime::createFromFormat(
                'Y-m-d',
                $startDate
            );

            // If the start day is before or the same as the schedule parameter then deduct 1 to $transactionsQuantity
            if ($startDateObject->format('d') <= $scheduleParameter) {
                $transactionsQuantity--;
            }

            $endDate = '';
            switch ($scheduleType) {
                case 1:
                    // Calculate how many days we need to sum
                    $addDays = $scheduleParameter * $transactionsQuantity;
                    $startDateObject->add(new \DateInterval('P' . $addDays  . 'D'));
                    $endDate = $startDateObject->format('Y-m-d');
                    break;
                case 2:
                    $startDateObject->add(new \DateInterval('P' . $transactionsQuantity . 'M'));
                    $endDate = $startDateObject->format('Y-m-' . $scheduleParameter);
                    break;
                case 3:
                    $startDateObject->add(new \DateInterval('P' . $transactionsQuantity . 'M'));
                    $endDate = $startDateObject->format('Y-m-t');
                    break;
                case 4:
                    // Find the real starting day
                    if ($startDateObject->format('l') !== $scheduleParameter) {
                        $startDateObject->modify('next ' . strtolower($scheduleParameter));
                    }

                    // Calculate how many days we need to add
                    $addDays = 7 * $transactionsQuantity;
                    $startDateObject->add(new \DateInterval('P' . $addDays  . 'D'));
                    $endDate = $startDateObject->format('Y-m-d');
                    break;
                default:
                    return null;
                    break;
            }
            return $endDate;
        }

        return null;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RecurringTransactions  $recurring_transaction
     * @return \Illuminate\Http\Response
     */
    public function show(RecurringTransactions $recurring_transaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RecurringTransactions  $recurring_transaction
     * @return \Illuminate\Http\Response
     */
    public function edit(RecurringTransactions $recurring_transaction)
    {
        $data = [
            'item' => $recurring_transaction,
            'bankAccounts' => BankAccount::orderBy('name')->select('id', 'name')->get(),
            'categories' => Category::orderBy('name')->select('id', 'name')->get(),
        ];

        $scheduleParameterKey = '';
        switch ($recurring_transaction->schedule_type) {
            case 1:
                $scheduleParameterKey = 'dayRepeat';
                break;
            case 2:
                $scheduleParameterKey = 'dayOfTheMonth';
                break;
            case 4:
                $scheduleParameterKey = 'dayOfTheWeek';
                break;
        }

        $data[$scheduleParameterKey] = $recurring_transaction->schedule_parameter;

        return Inertia::render(self::EDIT_VIEW, $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RecurringTransactions  $recurring_transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RecurringTransactions $recurring_transaction)
    {
        $request->validate(array_merge(
            ['item.name' => 'required|unique:recurring_transactions,name,' . $recurring_transaction->id . ',id'],
            self::VALIDATIONS
        ));

        $recurring_transaction->name = $request->item['name'];
        $recurring_transaction->bank_account_id = $request->item['bankAccountId'];
        $recurring_transaction->category_id = $request->item['categoryId'];
        $recurring_transaction->amount = $request->item['amount'];
        $recurring_transaction->start_date = $request->item['startDate'];
        $recurring_transaction->description = $request->item['description'];
        $recurring_transaction->schedule_type = $request->item['scheduleType'];
        $recurring_transaction->schedule_parameter = $request->item['scheduleParameter'];
        $recurring_transaction->limit_type = $request->item['limitType'];
        $recurring_transaction->active = $request->item['active'] ?? false;

        $recurring_transaction->end_date = null;
        $recurring_transaction->quantity = null;

        if (!empty($request->item['quantity'])) {
            $recurring_transaction->quantity = $request->item['quantity'];
            $recurring_transaction->end_date = $this->getEndDate($request->item);
        } else if (!empty($request->item['endDate'])) {
            $recurring_transaction->end_date = $request->item['endDate'];
        }

        $recurring_transaction->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RecurringTransactions  $recurring_transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(RecurringTransactions $recurring_transaction)
    {
        $recurring_transaction->delete();
    }

    public static function getDaysToAdd($recurringTrasnaction, $fromDate, $toDate)
    {
        $days = [];

        if (
            $recurringTrasnaction->schedule_type === 2 ||
            $recurringTrasnaction->schedule_type === 3
        ) {

            $days[] = $fromDate->format('Y-m-') . $recurringTrasnaction->schedule_parameter;

        } else {
        }

        return $days;
    }
}
