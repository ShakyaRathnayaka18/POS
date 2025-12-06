{{-- Global Search Modal --}}
<div x-data="globalSearch()"
     x-init="init()"
     @open-global-search.window="open()"
     @keydown.window.prevent.ctrl.k="open()"
     @keydown.window.prevent.meta.k="open()"
     x-cloak>

    {{-- Backdrop --}}
    <div x-show="isOpen"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-sm"
         @click="close()">
    </div>

    {{-- Modal Container --}}
    <div x-show="isOpen"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-start justify-center pt-16 sm:pt-24 px-4"
         @keydown.escape.window="close()">

        <div class="w-full max-w-3xl bg-white dark:bg-gray-800 rounded-xl shadow-2xl overflow-hidden flex"
             @click.stop
             style="max-height: calc(100vh - 8rem);">

            {{-- Main Search Panel --}}
            <div class="flex-1 flex flex-col min-w-0" :class="previewItem ? 'border-r border-gray-200 dark:border-gray-700' : ''">
                {{-- Search Header --}}
                <div class="flex items-center px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                    <i class="fas fa-search text-gray-400 mr-3"></i>
                    <input type="text"
                           x-ref="searchInput"
                           x-model="query"
                           @input.debounce.300ms="search()"
                           @keydown.arrow-down.prevent="navigateDown()"
                           @keydown.arrow-up.prevent="navigateUp()"
                           @keydown.enter.prevent="selectCurrent()"
                           @keydown.arrow-right.prevent="showPreviewForCurrent()"
                           placeholder="Search products, sales, suppliers, customers..."
                           class="flex-1 bg-transparent border-none outline-none text-gray-900 dark:text-white placeholder-gray-400 text-lg">
                    <div class="flex items-center gap-2 text-gray-400 text-sm">
                        <kbd class="px-1.5 py-0.5 bg-gray-100 dark:bg-gray-700 rounded text-xs">ESC</kbd>
                        <span>to close</span>
                    </div>
                </div>

                {{-- Results Container --}}
                <div class="flex-1 overflow-y-auto">
                    {{-- Loading State --}}
                    <div x-show="isLoading" class="flex items-center justify-center py-12">
                        <i class="fas fa-spinner fa-spin text-2xl text-gray-400"></i>
                    </div>

                    {{-- Empty State (No Query) --}}
                    <div x-show="!isLoading && !query" class="py-12 text-center">
                        <i class="fas fa-search text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400">Start typing to search across your data</p>
                        <div class="mt-4 flex flex-wrap justify-center gap-2 text-xs text-gray-400">
                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded">Products</span>
                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded">Sales</span>
                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded">Suppliers</span>
                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded">Customers</span>
                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded">Stocks</span>
                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded">& more</span>
                        </div>
                    </div>

                    {{-- No Results State --}}
                    <div x-show="!isLoading && query && total === 0" class="py-12 text-center">
                        <i class="fas fa-search-minus text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400">No results found for "<span x-text="query" class="font-medium"></span>"</p>
                        <p class="text-sm text-gray-400 mt-2">Try a different search term</p>
                    </div>

                    {{-- Results --}}
                    <div x-show="!isLoading && query && total > 0">
                        <template x-for="(items, category) in results" :key="category">
                            <div x-show="items && items.length > 0" class="border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                                {{-- Category Header --}}
                                <div class="px-4 py-2 bg-gray-50 dark:bg-gray-900/50 sticky top-0">
                                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400" x-text="formatCategory(category)"></span>
                                </div>

                                {{-- Category Items --}}
                                <template x-for="(item, index) in items" :key="item.id">
                                    <div @click="selectItem(item)"
                                         @mouseenter="previewItem = item; selectedIndex = flatIndex(category, index)"
                                         :class="{
                                             'bg-primary-50 dark:bg-primary-900/20': selectedIndex === flatIndex(category, index),
                                             'hover:bg-gray-50 dark:hover:bg-gray-700/50': selectedIndex !== flatIndex(category, index)
                                         }"
                                         class="flex items-center gap-3 px-4 py-3 cursor-pointer transition-colors">

                                        {{-- Icon --}}
                                        <div class="flex-shrink-0 w-10 h-10 rounded-lg flex items-center justify-center"
                                             :class="getColorClass(item.color)">
                                            <i class="fas" :class="item.icon"></i>
                                        </div>

                                        {{-- Content --}}
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2">
                                                <span class="font-medium text-gray-900 dark:text-white truncate" x-text="item.title"></span>
                                                <span class="text-xs px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400" x-text="item.subtitle"></span>
                                            </div>
                                            <p x-show="item.description" class="text-sm text-gray-500 dark:text-gray-400 truncate" x-text="item.description"></p>
                                        </div>

                                        {{-- Arrow --}}
                                        <i class="fas fa-chevron-right text-gray-300 dark:text-gray-600"></i>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-4 py-2 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 text-xs text-gray-400 flex items-center gap-4">
                    <span><kbd class="px-1 py-0.5 bg-gray-200 dark:bg-gray-700 rounded">↑↓</kbd> navigate</span>
                    <span><kbd class="px-1 py-0.5 bg-gray-200 dark:bg-gray-700 rounded">↵</kbd> select</span>
                    <span><kbd class="px-1 py-0.5 bg-gray-200 dark:bg-gray-700 rounded">→</kbd> preview</span>
                    <span class="ml-auto" x-show="total > 0"><span x-text="total"></span> results</span>
                </div>
            </div>

            {{-- Preview Panel --}}
            <div x-show="previewItem"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-x-4"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 class="w-80 flex-shrink-0 bg-gray-50 dark:bg-gray-900/50 flex flex-col">

                <template x-if="previewItem">
                    <div class="flex flex-col h-full">
                        {{-- Preview Header --}}
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-12 h-12 rounded-lg flex items-center justify-center"
                                     :class="getColorClass(previewItem.color)">
                                    <i class="fas text-xl" :class="previewItem.icon"></i>
                                </div>
                                <div>
                                    <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-full"
                                          :class="getBadgeClass(previewItem.color)"
                                          x-text="formatCategory(previewItem.type)"></span>
                                </div>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="previewItem.title"></h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400" x-text="previewItem.subtitle"></p>
                        </div>

                        {{-- Preview Meta --}}
                        <div class="flex-1 overflow-y-auto p-4">
                            <p x-show="previewItem.description" class="text-sm text-gray-600 dark:text-gray-300 mb-4" x-text="previewItem.description"></p>

                            <div class="space-y-3">
                                <template x-for="(value, key) in previewItem.meta" :key="key">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500 dark:text-gray-400" x-text="key"></span>
                                        <span class="font-medium text-gray-900 dark:text-white" x-text="value"></span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Preview Footer --}}
                        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                            <button @click="selectItem(previewItem)"
                                    class="w-full px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                                <span>View Details</span>
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<script>
function globalSearch() {
    return {
        isOpen: false,
        query: '',
        results: {},
        isLoading: false,
        selectedIndex: 0,
        previewItem: null,
        total: 0,
        flatResults: [],

        init() {
            // Listen for custom event to open search
            this.$watch('results', () => {
                this.buildFlatResults();
            });
        },

        open() {
            this.isOpen = true;
            this.query = '';
            this.results = {};
            this.total = 0;
            this.selectedIndex = 0;
            this.previewItem = null;
            this.$nextTick(() => {
                this.$refs.searchInput.focus();
            });
        },

        close() {
            this.isOpen = false;
            this.previewItem = null;
        },

        async search() {
            if (this.query.length < 2) {
                this.results = {};
                this.total = 0;
                this.previewItem = null;
                return;
            }

            this.isLoading = true;

            try {
                const response = await fetch(`{{ route('api.global-search') }}?q=${encodeURIComponent(this.query)}`);
                const data = await response.json();
                this.results = data.results;
                this.total = data.total;
                this.selectedIndex = 0;

                // Auto-select first result for preview
                if (this.flatResults.length > 0) {
                    this.previewItem = this.flatResults[0];
                } else {
                    this.previewItem = null;
                }
            } catch (error) {
                console.error('Search error:', error);
                this.results = {};
                this.total = 0;
            } finally {
                this.isLoading = false;
            }
        },

        buildFlatResults() {
            this.flatResults = [];
            for (const category in this.results) {
                if (this.results[category] && this.results[category].length > 0) {
                    this.flatResults.push(...this.results[category]);
                }
            }
        },

        flatIndex(category, index) {
            let offset = 0;
            for (const cat in this.results) {
                if (cat === category) {
                    return offset + index;
                }
                if (this.results[cat]) {
                    offset += this.results[cat].length;
                }
            }
            return offset + index;
        },

        navigateDown() {
            if (this.flatResults.length === 0) return;
            this.selectedIndex = (this.selectedIndex + 1) % this.flatResults.length;
            this.previewItem = this.flatResults[this.selectedIndex];
        },

        navigateUp() {
            if (this.flatResults.length === 0) return;
            this.selectedIndex = this.selectedIndex === 0
                ? this.flatResults.length - 1
                : this.selectedIndex - 1;
            this.previewItem = this.flatResults[this.selectedIndex];
        },

        selectCurrent() {
            if (this.flatResults.length > 0 && this.flatResults[this.selectedIndex]) {
                this.selectItem(this.flatResults[this.selectedIndex]);
            }
        },

        showPreviewForCurrent() {
            if (this.flatResults.length > 0 && this.flatResults[this.selectedIndex]) {
                this.previewItem = this.flatResults[this.selectedIndex];
            }
        },

        selectItem(item) {
            if (item && item.url) {
                window.location.href = item.url;
            }
        },

        formatCategory(category) {
            const labels = {
                'products': 'Products',
                'customers': 'Customers',
                'suppliers': 'Suppliers',
                'sales': 'Sales',
                'stocks': 'Stocks',
                'batches': 'Batches',
                'grns': 'GRNs',
                'employees': 'Employees',
                'vendor_codes': 'Vendor Codes',
                'product': 'Product',
                'customer': 'Customer',
                'supplier': 'Supplier',
                'sale': 'Sale',
                'stock': 'Stock',
                'batch': 'Batch',
                'grn': 'GRN',
                'employee': 'Employee',
                'vendor_code': 'Vendor Code'
            };
            return labels[category] || category;
        },

        getColorClass(color) {
            const colors = {
                'blue': 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400',
                'green': 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400',
                'purple': 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400',
                'yellow': 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400',
                'indigo': 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400',
                'pink': 'bg-pink-100 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400',
                'orange': 'bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400',
                'teal': 'bg-teal-100 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400',
                'gray': 'bg-gray-100 dark:bg-gray-900/30 text-gray-600 dark:text-gray-400'
            };
            return colors[color] || colors['gray'];
        },

        getBadgeClass(color) {
            const badges = {
                'blue': 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300',
                'green': 'bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300',
                'purple': 'bg-purple-100 dark:bg-purple-900/50 text-purple-700 dark:text-purple-300',
                'yellow': 'bg-yellow-100 dark:bg-yellow-900/50 text-yellow-700 dark:text-yellow-300',
                'indigo': 'bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300',
                'pink': 'bg-pink-100 dark:bg-pink-900/50 text-pink-700 dark:text-pink-300',
                'orange': 'bg-orange-100 dark:bg-orange-900/50 text-orange-700 dark:text-orange-300',
                'teal': 'bg-teal-100 dark:bg-teal-900/50 text-teal-700 dark:text-teal-300',
                'gray': 'bg-gray-100 dark:bg-gray-900/50 text-gray-700 dark:text-gray-300'
            };
            return badges[color] || badges['gray'];
        }
    };
}
</script>
