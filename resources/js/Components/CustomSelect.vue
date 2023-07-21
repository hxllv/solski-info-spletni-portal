<script setup>
import { onMounted, ref } from 'vue';
import { useFocusWithin } from '@vueuse/core'
import { router } from '@inertiajs/vue3';

const props = defineProps({
    modelValue: [String, Number],
    data: Object,
    termName: {
        type: String,
        default: 'cs_term',
    },
    perPageName: {
        type: String,
        default: 'cs_per',
    }
});

const emit = defineEmits(['update:modelValue']);

const term = ref(null);
const noOfItems = ref(null);

const input = ref(null);
const scrollable = ref(null);
const scrollPos = ref(null)
const { focused } = useFocusWithin(input)

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }

    scrollPos.value = scrollable.value.pageYOffset;
});

const emitData = (val) => {
    emit('update:modelValue', val);
    input.value.blur();
}

const filterData = () => {
    router.get(
        props.data.path,
        {
            [props.termName]: term.value,
            [props.perPageName]: 10,
        }, 
        {
            preserveState: true,
            preserveScroll: true,
        },
    )
}

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <div ref="input" tabindex="0" class="relative">
        <input type="text" v-model="term" @input="filterData" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        <div ref="scrollable" v-show="focused" class="px-2 absolute right-0 left-0">
            <div class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-b-lg max-h-44 overflow-y-auto">
                <div v-for="item in data.data" @click="emitData(item.id)" class="p-1">
                    {{ item }}
                </div> 
            </div>
        </div>
    </div>
</template>