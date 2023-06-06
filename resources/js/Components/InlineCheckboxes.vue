<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import Checkbox from '@/Components/Checkbox.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    items: Array,
    form: Object
});

const emit = defineEmits(['formChange']);

const emitter = () => {
    emit('formChange', form.value)
}

const form = ref(props.form)
</script>

<template>
    <ul class="items-center justify-center w-full text-sm font-medium text-gray-900 flex">
        <li class="w-full" v-for="item in items">
            <div class="flex items-center pl-3">
                <InputLabel :for="item.id">
                    <div class="flex items-center">
                        <Checkbox :id="item.id" v-model:checked="form[item.id]" :name="item.id" required @change="emitter"/>
                        <div class="ml-2">
                            {{ item.name }}
                        </div>
                    </div>
                </InputLabel>
            </div>
        </li>
    </ul>
</template>