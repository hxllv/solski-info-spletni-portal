<script setup>
import InlineCheckboxes from '@/Components/InlineCheckboxes.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    password: '',
    password_confirmation: '',
    terms: false,
});

const emitter = (form) => {
    console.log(form)
}

const users = [
    { name: 'View', id: 'users.view'},
    { name: 'Invite', id: 'users.invite'},
    { name: 'Edit', id: 'users.edit'},
    { name: 'Delete', id: 'users.delete'}
]
</script>

<template>
    <div class="col-span-6 sm:col-span-4">
        <h1 class="mb-4 font-semibold text-gray-900 text-xl">Pravice</h1>
        <InlineCheckboxes :items="users" @permissionFormChange="emitter"/>
    </div>
</template>