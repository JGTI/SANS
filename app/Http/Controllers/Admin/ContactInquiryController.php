<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateContactInquiryRequest;
use App\Models\ContactInquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ContactInquiryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            $query = ContactInquiry::query()->select(['id', 'name', 'email', 'company', 'service', 'status', 'created_at']);
            if (in_array($request->query('status'), ['new', 'in_progress', 'closed'], true)) {
                $query->where('status', $request->query('status'));
            }

            return DataTables::eloquent($query)
                ->editColumn('created_at', fn (ContactInquiry $inquiry) => $inquiry->created_at->format('d/m/Y H:i'))
                ->toJson();
        }

        return view('admin.contacts');
    }

    public function show(ContactInquiry $contact)
    {
        return response()->json($contact);
    }

    public function update(UpdateContactInquiryRequest $request, ContactInquiry $contact)
    {
        DB::transaction(function () use ($request, $contact) {
            $contact = ContactInquiry::lockForUpdate()->findOrFail($contact->id);
            $previous = $contact->status;
            $contact->update($request->validated());
            activity('administration')->causedBy($request->user())->performedOn($contact)
                ->withProperties(['previous_status' => $previous, 'status' => $contact->status])
                ->log('Estado de contacto actualizado');
        });

        return response()->json(['message' => 'Estado actualizado.']);
    }
}
