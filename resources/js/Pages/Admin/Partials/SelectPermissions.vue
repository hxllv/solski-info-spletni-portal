<script setup>
import InlineCheckboxes from '@/Components/InlineCheckboxes.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Checkbox from '@/Components/Checkbox.vue';
import { ref, computed } from 'vue';

const emit = defineEmits(['formChange']);

const props = defineProps({
    data: Array,
});

const inclds = (...names) => {
    let newData = {}

    if (!names.length) {
        props.data.forEach(dat => {
            newData[dat] = true
        })

        return newData
    } 

    names.forEach(name => {
        newData[name] = false
        if (props.data.includes(name))
            newData[name] = true
    })

    return newData
}

let form = ref({
    'admin.panel.view': false,
    'users.view': false,
    'users.invite': false,
    'users.edit': false,
    'users.delete': false,
    'roles.view': false,
    'roles.create': false,
    'roles.edit': false,
    'roles.delete': false,
    ...inclds()
});

const updateForm = (newForm) => {
    form.value = {
        ...form.value,
        ...newForm
    }

    emitter()
}

const emitter = () => {
    emit('formChange', form.value)
}

const users = {
    arr: [
        { name: 'View', id: 'users.view'},
        { name: 'Invite', id: 'users.invite'},
        { name: 'Edit', id: 'users.edit'},
        { name: 'Delete', id: 'users.delete'}
    ],
    form: {
        ...inclds('users.view', 'users.invite', 'users.edit', 'users.delete')
    }
}

const roles = {
    arr: [
        { name: 'View', id: 'roles.view'},
        { name: 'Create', id: 'roles.create'},
        { name: 'Edit', id: 'roles.edit'},
        { name: 'Delete', id: 'roles.delete'}
    ],
    form: {
        ...inclds('roles.view', 'roles.create', 'roles.edit', 'roles.delete')
    }
}
</script>

<template>
    <div class="col-span-6 sm:col-span-4">
        <h1 class="mb-4 font-semibold text-gray-900 text-xl">Pravice</h1>
        <table class="w-full text-sm text-left text-gray-500">
            <tr class="bg-white hover:bg-gray-50 border-b">
                <div class="m-2 text-xl">
                    <InputLabel for="admin.panel.view">
                        <div class="flex items-center">
                            <Checkbox id="admin.panel.view" v-model:checked="form['admin.panel.view']" name="admin.panel.view" required @change="emitter"/>
                            <div class="ml-2">
                                Admin panel
                            </div>
                        </div>
                    </InputLabel>
                </div>
            </tr>
            <div :class="[form['admin.panel.view'] ? '' : 'pointer-events-none opacity-30']">
                <h1 class="font-semibold text-gray-900 text-lg m-2">Users</h1>
                <table class="w-full">
                    <tr class="bg-white hover:bg-gray-50">
                        <div class="m-2 text-xl">
                            <InlineCheckboxes :items="users.arr" :form="users.form" @formChange="updateForm"/>
                        </div>
                    </tr>
                </table>
                <h1 class="font-semibold text-gray-900 text-lg m-2">Roles</h1>
                <table class="w-full">
                    <tr class="bg-white hover:bg-gray-50">
                        <div class="m-2 text-xl">
                            <InlineCheckboxes :items="roles.arr" :form="roles.form" @formChange="updateForm"/>
                        </div>
                    </tr>
                </table>
            </div>
        </table>
    </div>
</template>