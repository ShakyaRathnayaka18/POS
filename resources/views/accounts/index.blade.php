@extends('layouts.app')

@section('title', 'Chart of Accounts')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Chart of Accounts</h1>
        @can('create accounts')
        <a href="{{ route('accounts.create') }}" class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700">
            <i class="fas fa-plus mr-2"></i>Add Account
        </a>
        @endcan
    </div>

    <!-- Account Types Filter -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <div class="flex space-x-4">
            @foreach($accountTypes as $type)
            <button onclick="filterByType('{{ $type->name }}')"
                class="px-4 py-2 rounded-md text-sm font-medium transition-colors
                    {{ request('type') == $type->name ? 'bg-primary-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }}">
                {{ $type->name }}
            </button>
            @endforeach
            <button onclick="filterByType('')"
                class="px-4 py-2 rounded-md text-sm font-medium transition-colors
                    {{ !request('type') ? 'bg-primary-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }}">
                All Accounts
            </button>
        </div>
    </div>

    <!-- Accounts Table -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Account Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Account Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Account Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Parent Account</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                @forelse($accounts as $account)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                        {{ $account->account_code }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                        {{ $account->account_name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            @if($account->accountType->name == 'Asset') bg-blue-100 text-blue-800
                            @elseif($account->accountType->name == 'Liability') bg-red-100 text-red-800
                            @elseif($account->accountType->name == 'Equity') bg-purple-100 text-purple-800
                            @elseif($account->accountType->name == 'Revenue') bg-green-100 text-green-800
                            @else bg-yellow-100 text-yellow-800
                            @endif">
                            {{ $account->accountType->name }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ $account->parentAccount?->account_name ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            {{ $account->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $account->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                        <a href="{{ route('accounts.show', $account) }}" class="text-primary-600 hover:text-primary-900 dark:text-primary-400">
                            <i class="fas fa-eye"></i>
                        </a>
                        @can('edit accounts')
                        <a href="{{ route('accounts.edit', $account) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400">
                            <i class="fas fa-edit"></i>
                        </a>
                        @endcan
                        @can('delete accounts')
                        <form action="{{ route('accounts.destroy', $account) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Are you sure you want to delete this account?')" class="text-red-600 hover:text-red-900 dark:text-red-400">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                        No accounts found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
function filterByType(type) {
    const url = new URL(window.location.href);
    if (type) {
        url.searchParams.set('type', type);
    } else {
        url.searchParams.delete('type');
    }
    window.location.href = url.toString();
}
</script>
@endsection
