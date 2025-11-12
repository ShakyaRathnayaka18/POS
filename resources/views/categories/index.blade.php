@extends('layouts.app')

@section('title', 'Categories')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Categories</h1>
        <button onclick="openModal('categoryModal')" class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 border" style="border-color: #4ea9dd; border-width: 1px;">
            <i class="fas fa-plus mr-2"></i>Add Category
        </button>
    </div>

    <!-- Categories Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($categories as $category)
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                    @if($category->icon && file_exists(public_path('images/category-icons/' . $category->icon)))
                        <img src="{{ asset('images/category-icons/' . $category->icon) }}" alt="{{ $category->cat_name }}" class="w-12 h-12 object-contain">
                    @else
                        <i class="fas fa-box text-blue-600 dark:text-blue-400 text-2xl"></i>
                    @endif
                </div>
                <div class="flex space-x-2">
                    <button class="text-gray-400 hover:text-gray-600" onclick="openEditModal({{ $category->id }}, '{{ addslashes($category->cat_name) }}', '{{ addslashes($category->description) }}', '{{ $category->icon }}')">
                        <i class="fas fa-edit"></i>
                    </button>
                    <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-600">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $category->cat_name }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $category->description }}</p>
            <div class="mt-4 flex items-center justify-between">
                <span class="text-sm text-gray-500 dark:text-gray-400">Products</span>
                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $category->products->count() }}</span>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Category Modal (Create) -->
<div id="categoryModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Add New Category</h3>
                <button onclick="closeModal('categoryModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form class="space-y-4" method="POST" action="{{ route('categories.store') }}">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category Name *</label>
                    <input name="cat_name" type="text" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Icon</label>
                    <select name="icon" id="create_icon" onchange="updateIconPreview('create')" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Select Icon</option>
                        <option value="beverages.png">Beverages</option>
                        <option value="bakery.png">Bakery</option>
                        <option value="dairy.png">Dairy</option>
                        <option value="meat.png">Meat</option>
                        <option value="seafood.png">Seafood</option>
                        <option value="fruits.png">Fruits</option>
                        <option value="vegetables.png">Vegetables</option>
                        <option value="frozen.png">Frozen Foods</option>
                        <option value="snacks.png">Snacks</option>
                        <option value="sweets.png">Sweets</option>
                        <option value="spices.png">Spices</option>
                        <option value="grains.png">Grains</option>
                        <option value="canned.png">Canned Goods</option>
                        <option value="cleaning.png">Cleaning Products</option>
                        <option value="personal-care.png">Personal Care</option>
                        <option value="baby.png">Baby Products</option>
                        <option value="medicine.png">Health & Medicine</option>
                        <option value="electronics.png">Electronics</option>
                        <option value="pet-supplies.png">Pet Supplies</option>
                        <option value="household.png">Household Items</option>
                        <option value="stationery.png">Stationery</option>
                    </select>
                    <div id="create_icon_preview" class="mt-2 flex items-center justify-center w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-lg hidden">
                        <img id="create_icon_image" src="" alt="Icon Preview" class="w-12 h-12 object-contain">
                    </div>
                </div>
                <div class="flex items-center justify-end space-x-4 pt-4">
                    <button type="button" onclick="closeModal('categoryModal')" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">Cancel</button>
                    <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 border" style="border-color: #4ea9dd; border-width: 1px;">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Category Modal (Edit) -->
<div id="editCategoryModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Edit Category</h3>
                <button onclick="closeModal('editCategoryModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="editCategoryForm" class="space-y-4" method="POST">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category Name *</label>
                    <input id="edit_cat_name" name="cat_name" type="text" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                    <textarea id="edit_description" name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Icon</label>
                    <select id="edit_icon" name="icon" onchange="updateIconPreview('edit')" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Select Icon</option>
                        <option value="beverages.png">Beverages</option>
                        <option value="bakery.png">Bakery</option>
                        <option value="dairy.png">Dairy</option>
                        <option value="meat.png">Meat</option>
                        <option value="seafood.png">Seafood</option>
                        <option value="fruits.png">Fruits</option>
                        <option value="vegetables.png">Vegetables</option>
                        <option value="frozen.png">Frozen Foods</option>
                        <option value="snacks.png">Snacks</option>
                        <option value="sweets.png">Sweets</option>
                        <option value="spices.png">Spices</option>
                        <option value="grains.png">Grains</option>
                        <option value="canned.png">Canned Goods</option>
                        <option value="cleaning.png">Cleaning Products</option>
                        <option value="personal-care.png">Personal Care</option>
                        <option value="baby.png">Baby Products</option>
                        <option value="medicine.png">Health & Medicine</option>
                        <option value="electronics.png">Electronics</option>
                        <option value="pet-supplies.png">Pet Supplies</option>
                        <option value="household.png">Household Items</option>
                        <option value="stationery.png">Stationery</option>
                    </select>
                    <div id="edit_icon_preview" class="mt-2 flex items-center justify-center w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-lg hidden">
                        <img id="edit_icon_image" src="" alt="Icon Preview" class="w-12 h-12 object-contain">
                    </div>
                </div>
                <div class="flex items-center justify-end space-x-4 pt-4">
                    <button type="button" onclick="closeModal('editCategoryModal')" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">Cancel</button>
                    <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 border" style="border-color: #4ea9dd; border-width: 1px;">Update Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

function openEditModal(id, name, description, icon) {
    document.getElementById('edit_cat_name').value = name;
    document.getElementById('edit_description').value = description;
    document.getElementById('edit_icon').value = icon;
    document.getElementById('editCategoryForm').action = '/categories/' + id;

    // Show icon preview if icon exists
    if (icon) {
        updateIconPreview('edit');
    }

    openModal('editCategoryModal');
}

function updateIconPreview(type) {
    const select = document.getElementById(type + '_icon');
    const preview = document.getElementById(type + '_icon_preview');
    const image = document.getElementById(type + '_icon_image');
    const selectedIcon = select.value;

    if (selectedIcon) {
        image.src = '/images/category-icons/' + selectedIcon;
        preview.classList.remove('hidden');
    } else {
        preview.classList.add('hidden');
    }
}
</script>
@endpush
@endsection
