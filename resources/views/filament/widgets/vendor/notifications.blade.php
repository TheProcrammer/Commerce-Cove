<x-filament::widget>
    <x-filament::card>
        <div class="flex justify-between items-center mb-3">
            <h2 class="text-lg font-bold mb-5">Notifications</h2>
            <x-filament::badge color="warning" size="xl" class="justify-center">
                    5
            </x-filament::badge>
        </div>
        

        <ul class="space-y-2">
            <!-- Fake Notifications (Visual Only) -->
            <li class="flex items-center justify-between p-2">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center" style="background-color: #bbf7d0;">
                        <x-heroicon-o-banknotes class="w-5 h-5" />
                    </div>                    
                    <div>
                        <strong class="text-sm">Refund Processed</strong>
                        <p class="text-xs text-gray-600">Your refund for Order #12345 has been processed.</p>
                        <span class="text-xs text-gray-400">2 hours ago</span>
                    </div>
                </div>
                <button class="text-gray-400 hover:text-red-500">
                    <x-heroicon-o-x-mark class="w-4 h-4" />
                </button>
            </li>

            <li class="flex items-center justify-between p-2">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center" style="background-color: #dbeafe;">
                        <x-heroicon-o-arrow-uturn-left class="w-5 h-5" />
                    </div>
                    <div>
                        <strong class="text-sm">Return Approved</strong>
                        <p class="text-xs text-gray-600">Your return request for Order #67890 has been approved.</p>
                        <span class="text-xs text-gray-400">3 hours ago</span>
                    </div>
                </div>
                <button class="text-gray-400 hover:text-red-500">
                    <x-heroicon-o-x-mark class="w-4 h-4" />
                </button>
            </li>

            <li class="flex items-center justify-between p-2">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background-color: #fef9c3;">
                        <x-heroicon-o-x-circle class="w-5 h-5" />
                    </div>
                    <div>
                        <strong class="text-sm">Order Cancelled</strong>
                        <p class="text-xs text-gray-600">Your order #45678 has been cancelled.</p>
                        <span class="text-xs text-gray-400">5 hours ago</span>
                    </div>
                </div>
                <button class="text-gray-400 hover:text-red-500">
                    <x-heroicon-o-x-mark class="w-4 h-4" />
                </button>
            </li>

            <li class="flex items-center justify-between p-2">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center" style="background-color: #bbf7d0;">
                        <x-heroicon-o-star class="w-5 h-5" />
                    </div>
                    <div>
                        <strong class="text-sm">New Review Received</strong>
                        <p class="text-xs text-gray-600">You received a new 5-star review on Product XYZ.</p>
                        <span class="text-xs text-gray-400">8 hours ago</span>
                    </div>
                </div>
                <button class="text-gray-400 hover:text-red-500">
                    <x-heroicon-o-x-mark class="w-4 h-4" />
                </button>
            </li>

            <li class="flex items-center justify-between p-2">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center" style="background-color: #bbf7d0;">
                        <x-heroicon-o-truck class="w-5 h-5"/>
                    </div>
                    <div>
                        <strong class="text-sm">Order Delivered</strong>
                        <p class="text-xs text-gray-600">Your order #78901 has been successfully delivered.</p>
                        <span class="text-xs text-gray-400">1 day ago</span>
                    </div>
                </div>
                <button class="text-gray-400 hover:text-red-500">
                    <x-heroicon-o-x-mark class="w-4 h-4" />
                </button>
            </li>
        </ul>

        <div class="mt-3 flex justify-end pr-2">
            <x-filament::button color="primary" size="sm" class="mt-3">
                View All
            </x-filament::button>
        </div>

    </x-filament::card>
</x-filament::widget>
