<script setup>
import InlineCheckboxes from '@/Components/InlineCheckboxes.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Checkbox from '@/Components/Checkbox.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const emit = defineEmits(['formChange']);

let form = useForm({
    'admin.panel.view': false,
    'users.view': false,
    'users.invite': false,
    'users.edit': false,
    'users.delete': false
});

const updateForm = (newForm) => {
    console.log(newForm)
    form = {
        ...newForm
    }

    emitter()
}

const emitter = () => {
    console.log(form)
}

const users = {
    arr: [
        { name: 'View', id: 'users.view'},
        { name: 'Invite', id: 'users.invite'},
        { name: 'Edit', id: 'users.edit'},
        { name: 'Delete', id: 'usersDelete'}
    ],
    form: {
        'users.view': false,
        'users.invite': false,
        'users.edit': false,
        'users.delete': false
    }
}
</script>

<template>
    <div class="col-span-6 sm:col-span-4">
        <h1 class="mb-4 font-semibold text-gray-900 text-xl">Pravice</h1>
        <InputLabel for="admin.panel.view">
            <div class="flex items-center">
                <Checkbox id="admin.panel.view" v-model:checked="form['admin.panel.view']" name="admin.panel.view" required @change="emitter"/>
                <div class="ml-2">
                    Admin panel
                </div>
            </div>
        </InputLabel>
        <InlineCheckboxes :items="users.arr" :form="users.form" @formChange="updateForm"/>
    </div>
</template>