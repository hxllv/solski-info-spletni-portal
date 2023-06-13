<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    data: Object,
    query: Object,
    pageName: String,
    routeName: String,
    routeParams: Array
});

//tole je pretty bad, sam je zarad tega pagination code manj ogaben

const helperArrays = computed(() => {
    return {
        first5: 
            [1, 2, 3, 4, 5],
        minus2toPlus2: 
            [props.data.current_page - 2, props.data.current_page - 1, props.data.current_page, props.data.current_page + 1, props.data.current_page + 2],
        last5: 
            [props.data.last_page - 4, props.data.last_page - 3, props.data.last_page - 2, props.data.last_page - 1, props.data.last_page],
    }
});

// izbira classa glede na to a smo na trenutnem pageu

const classes = "relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300";

const getClass = (pageNoTemp) => {
    return pageNoTemp == props.data.current_page ?
        `${classes} text-white bg-blue-500 cursor-default pointer-events-none` : `${classes} cursor-pointer hover:bg-gray-50`
}

// disabla chevrone glede na kje smo

const isAt = (str) => {
    switch (str) {
        case 'start':
            return props.data.current_page === 1 ? 'disabled text-gray-200 pointer-events-none' : 'text-gray-400 cursor-pointer hover:bg-gray-50' 
        case 'end': 
            return props.data.current_page == props.data.last_page ? 'disabled text-gray-200 pointer-events-none' : 'text-gray-400 cursor-pointer hover:bg-gray-50' 
    }
}

const isAtData = (str) => {
    switch (str) {
        case 'start':
            return props.data.current_page > 1 ? props.data.current_page - 1 : ''
        case 'end': 
            return props.data.current_page < props.data.last_page ? props.data.current_page + 1 : ''
    }
}
</script>

<template>
    <div class="flex items-center justify-between border-gray-200 bg-white px-4 py-3 sm:px-6 col-span-3  sm:rounded-b-md">
        <div class="flex flex-1 items-center justify-between">
            <div class="hidden sm:block">
                <p class="text-sm text-gray-700">
                    Showing
                    <span class="font-medium">{{ data.from }}</span>
                    to
                    <span class="font-medium">{{ data.to }}</span>
                    of
                    <span class="font-medium">{{ data.total }}</span>
                    results
                </p>
            </div>
            <div>
                <nav class="isolate inline-flex -space-x-px rounded-md" aria-label="Pagination">
                    <Link preserve-scroll preserve-state :href="route(routeName, ...routeParams)" :data="{ [pageName]: 1, ...query }" :class="isAt('start')" class="relative inline-flex items-center rounded-l-md px-2 py-2 ring-1 ring-inset ring-gray-300">
                        <span class="sr-only">Previous</span>
                        <font-awesome-icon icon="fa-solid fa-chevron-left" class="py-1" />
                        <font-awesome-icon icon="fa-solid fa-chevron-left" class="py-1" />
                    </Link>
                    <Link preserve-scroll preserve-state :href="route(routeName, ...routeParams)" :data="{ [pageName]: isAtData('start'), ...query }" :class="isAt('start')" class="relative inline-flex items-center px-2 py-2 ring-1 ring-inset ring-gray-300">
                        <span class="sr-only">Previous</span>
                        <font-awesome-icon icon="fa-solid fa-chevron-left" class="p-1" />
                    </Link>

                    <!-- ce mamo manj kot 6 strani -->
                    <template v-if="data.last_page < 6" v-for="(value, index) in Array(data.last_page)">
                        <Link preserve-scroll preserve-state :href="route(routeName, ...routeParams)" :data="{ [pageName]: index + 1, ...query }" :class="getClass(index + 1)">
                            {{ index + 1 }}
                        </Link>
                    </template>

                    <!-- ce mamo vec -->
                    <template v-if="data.last_page >= 6">
                        <template v-if="data.current_page <= 3" v-for="val in helperArrays.first5">
                            <Link preserve-scroll preserve-state :href="route(routeName, ...routeParams)" :data="{ [pageName]: val, ...query }" :class="getClass(val)">
                                {{ val }}
                            </Link>
                        </template>

                        <template v-if="data.current_page > 3 && data.current_page < data.last_page - 2" v-for="val in helperArrays.minus2toPlus2">
                            <Link preserve-scroll preserve-state :href="route(routeName, ...routeParams)" :data="{ [pageName]: val, ...query }" :class="getClass(val)">
                                {{ val }}
                            </Link>
                        </template>

                        <template v-if="data.current_page >= data.last_page - 2" v-for="val in helperArrays.last5">
                            <Link preserve-scroll preserve-state :href="route(routeName, ...routeParams)" :data="{ [pageName]: val, ...query }" :class="getClass(val)">
                                {{ val }}
                            </Link>
                        </template>
                    </template>

                    <Link preserve-scroll preserve-state :href="route(routeName, ...routeParams)" :data="{ [pageName]: isAtData('end'), ...query }" :class="isAt('end')" class="relative inline-flex items-center px-2 py-2 ring-1 ring-inset ring-gray-300">
                        <span class="sr-only">Previous</span>
                        <font-awesome-icon icon="fa-solid fa-chevron-right" class="p-1" />
                    </Link>
                    <Link preserve-scroll preserve-state :href="route(routeName, ...routeParams)" :data="{ [pageName]: data.last_page, ...query }" :class="isAt('end')" class="relative inline-flex items-center rounded-r-md px-2 py-2 ring-1 ring-inset ring-gray-300">
                        <span class="sr-only">Previous</span>
                        <font-awesome-icon icon="fa-solid fa-chevron-right" class="py-1" />
                        <font-awesome-icon icon="fa-solid fa-chevron-right" class="py-1" />
                    </Link>
                </nav>
            </div>
        </div>
    </div>
</template>