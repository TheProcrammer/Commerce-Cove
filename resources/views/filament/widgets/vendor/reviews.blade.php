<x-filament::widget>
    <x-filament::card>
        <!-- Header with Star Icon Badge -->
        <div class="flex justify-between items-center mb-3">
            <h2 class="text-lg font-bold mb-5">Reviews</h2>
            <x-filament::badge color="warning" size="xl" class="flex items-center gap-1">
                <x-heroicon-o-star class="w-4 h-4 inline-block"/> 
                <span>5</span>
            </x-filament::badge>
        </div>

        <!-- Review Notifications -->
        <ul class="space-y-2">
            <!-- New 5-Star Review -->
            <li class="flex items-center justify-between p-2">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center" style="background-color: #bbf7d0;">
                        <x-heroicon-o-star class="w-5 h-5" />
                    </div>                    
                    <div>
                        <strong class="text-sm">New 5-Star Review</strong>
                        <p class="text-xs text-gray-600">You received a new 5-star review on Product XYZ.</p>
                        <span class="text-xs text-gray-400">10 minutes ago</span>
                        <div class="mt-1 flex">
                            @for ($i = 1; $i <= 5; $i++)
                            <x-heroicon-o-star class="w-4 h-4" style="color: transparent; stroke: #D4A017; stroke-width: 2;" />
                            @endfor
                        </div>
                    </div>
                </div>
                <button class="text-gray-400 hover:text-red-500">
                    <x-heroicon-o-x-mark class="w-4 h-4" />
                </button>
            </li>

            <!-- Review Updated -->
            <li class="flex items-center justify-between p-2">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center" style="background-color: #FDE68A;">
                        <x-heroicon-o-pencil class="w-5 h-5" />
                    </div>
                    <div>
                        <strong class="text-sm">Review Updated</strong>
                        <p class="text-xs text-gray-600">A customer updated their review for Product ABC.</p>
                        <span class="text-xs text-gray-400">1 hour ago</span>
                        <div class="mt-1 flex">
                            @for ($i = 1; $i <= 5; $i++)
                            <x-heroicon-o-star class="w-4 h-4" style="color: transparent; stroke: #D4A017; stroke-width: 2;" />
                            @endfor
                        </div>
                    </div>
                </div>
                <button class="text-gray-400 hover:text-red-500">
                    <x-heroicon-o-x-mark class="w-4 h-4" />
                </button>
            </li>

            <!-- Positive Review Highlighted -->
            <li class="flex items-center justify-between p-2">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center" style="background-color: #bbf7d0;">
                        <x-heroicon-o-hand-thumb-up class="w-5 h-5" />
                    </div>
                    <div>
                        <strong class="text-sm">Positive Review Featured</strong>
                        <p class="text-xs text-gray-600">A 5-star review was featured on the homepage.</p>
                        <span class="text-xs text-gray-400">3 hours ago</span>
                        <div class="mt-1 flex">
                            @for ($i = 1; $i <= 5; $i++)
                            <x-heroicon-o-star class="w-4 h-4" style="color: transparent; stroke: #D4A017; stroke-width: 2;" />
                            @endfor
                        </div>
                    </div>
                </div>
                <button class="text-gray-400 hover:text-red-500">
                    <x-heroicon-o-x-mark class="w-4 h-4" />
                </button>
            </li>

            <!-- Low Rating Review -->
            <li class="flex items-center justify-between p-2">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center" style="background-color: #D0342C;">
                        <x-heroicon-o-hand-thumb-down class="w-5 h-5" />
                    </div>
                    <div>
                        <strong class="text-sm">Low Rating Review</strong>
                        <p class="text-xs text-gray-600">A customer left a 2-star review on Product XYZ.</p>
                        <span class="text-xs text-gray-400">5 hours ago</span>
                        <div class="mt-1 flex">
                            @for ($i = 1; $i <= 5; $i++)
                            <x-heroicon-o-star class="w-4 h-4" style="color: transparent; stroke: #D4A017; stroke-width: 2;" />
                            @endfor
                        </div>
                    </div>
                </div>
                <button class="text-gray-400 hover:text-red-500">
                    <x-heroicon-o-x-mark class="w-4 h-4" />
                </button>
            </li>
        </ul>

        <!-- View All Button -->
        <div class="mt-3 flex justify-end pr-2" style="margin-top: 18px;">
            <x-filament::button color="primary" size="sm" class="mt-3">
                View All Reviews
            </x-filament::button>
        </div>

    </x-filament::card>
</x-filament::widget>
