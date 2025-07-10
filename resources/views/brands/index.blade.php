@extends('layouts.app')

@section('title', 'Brands')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Brands</h1>
            <button onclick="openModal('brandModal')"
               class="bg-primary-600 text-[#2f85c3] dark:text-white px-4 py-2 rounded-md hover:bg-primary-700 border"
                style="border-color: #4ea9dd; border-width: 1px;">
                <i class="fas fa-plus mr-2"></i>Add Brand
            </button>
        </div>

        <!-- Brands Table -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Logo</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Brand</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Description</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Products</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                        @foreach($brands as $brand)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <img class="h-10 w-10 rounded-lg object-cover"
                                        src="{{ $brand->logo ?? 'https://via.placeholder.com/40' }}" alt="">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $brand->brand_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $brand->description }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $brand->products->count() ?? 0 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <button class="text-[#2f85c3] dark:text-white border border-[#2f85c3] dark:border-white rounded px-3 py-1 transition-colors duration-200 hover:bg-[#2f85c3] hover:text-white dark:hover:bg-white dark:hover:text-[#2f85c3]"
                                        onclick="openEditBrandModal({{ $brand->id }}, '{{ addslashes($brand->brand_name) }}', '{{ addslashes($brand->description ?? '') }}')">
                                        Edit
                                    </button>
                                    <form action="{{ route('brands.destroy', $brand) }}" method="POST" style="display:inline;"
                                        onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 dark:text-white border border-red-600 dark:border-white rounded px-3 py-1 transition-colors duration-200 hover:bg-red-600 hover:text-white dark:hover:bg-white dark:hover:text-red-600">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Brand Modal (Create) -->
        <div id="brandModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-60 overflow-y-auto h-full w-full hidden">
            <div
                class="relative w-full max-w-lg mx-auto my-8 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-trademark mr-2 text-primary-600"></i> Add New Brand
                    </h3>
                    <button onclick="closeModal('brandModal')" class="text-gray-400 hover:text-gray-600 transition">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                <form class="px-6 py-6 space-y-6" method="POST" action="{{ route('brands.store') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Brand Name <span
                                    class="text-red-500">*</span></label>
                            <input name="brand_name" type="text" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                                placeholder="e.g. Coca Cola">
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                            <textarea name="description" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"
                                placeholder="Short brand description..."></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Logo</label>
                            <label
                                class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100">
                                <div class="flex flex-col items-center justify-center pt-2 pb-2">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                    <p class="mb-1 text-xs text-gray-500 dark:text-gray-400"><span
                                            class="font-semibold">Click to upload</span> or drag and drop</p>
                                    <p class="text-xs text-gray-400">PNG, JPG or JPEG (MAX. 2MB)</p>
                                </div>
                                <input name="logo" type="file" class="hidden" accept="image/*">
                            </label>
                        </div>
                    </div>
                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" onclick="closeModal('brandModal')"
                            class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition">Cancel</button>
                        <button type="submit"
                            class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 transition font-semibold flex items-center"><i
                                class="fas fa-save mr-2"></i>Save Brand</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Brand Modal (Edit) -->
        <div id="editBrandModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-60 overflow-y-auto h-full w-full hidden">
            <div
                class="relative w-full max-w-lg mx-auto my-8 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-trademark mr-2 text-primary-600"></i> Edit Brand
                    </h3>
                    <button onclick="closeModal('editBrandModal')" class="text-gray-400 hover:text-gray-600 transition">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                <form id="editBrandForm" class="px-6 py-6 space-y-6" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Brand Name <span
                                    class="text-red-500">*</span></label>
                            <input id="edit_brand_name" name="brand_name" type="text" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                            <textarea id="edit_description" name="description" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Logo</label>
                            <label
                                class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100">
                                <div class="flex flex-col items-center justify-center pt-2 pb-2">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                    <p class="mb-1 text-xs text-gray-500 dark:text-gray-400"><span
                                            class="font-semibold">Click to upload</span> or drag and drop</p>
                                    <p class="text-xs text-gray-400">PNG, JPG or JPEG (MAX. 2MB)</p>
                                </div>
                                <input id="edit_logo" name="logo" type="file" class="hidden" accept="image/*">
                            </label>
                        </div>
                    </div>
                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" onclick="closeModal('editBrandModal')"
                            class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition">Cancel</button>
                        <button type="submit"
                            class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 transition font-semibold flex items-center"><i
                                class="fas fa-save mr-2"></i>Update Brand</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        function openEditBrandModal(brandId, brandName, description) {
            try {
                document.getElementById('edit_brand_name').value = brandName || '';
                document.getElementById('edit_description').value = description || '';
                document.getElementById('editBrandForm').action = '/brands/' + brandId;
                openModal('editBrandModal');
            } catch (error) {
                console.error('Error opening edit modal:', error);
                alert('An error occurred while opening the edit form. Please try again.');
            }
        }
    </script>
@endsection
