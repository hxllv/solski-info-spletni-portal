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
    'teacher': false,
    'class.teacher': false,
    'admin.panel.view': false,
    'users.view': false,
    'users.invite': false,
    'users.edit': false,
    'users.delete': false,
    'roles.view': false,
    'roles.create': false,
    'roles.edit': false,
    'roles.delete': false,
    'classes.view': false,
    'classes.create': false,
    'classes.edit': false,
    'classes.delete': false,
    'subjects.view': false,
    'subjects.create': false,
    'subjects.edit': false,
    'subjects.delete': false,
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
        { name: 'Pogled', id: 'users.view'},
        { name: 'Povabljanje', id: 'users.invite'},
        { name: 'Urejanje', id: 'users.edit'},
        { name: 'Brisanje', id: 'users.delete'}
    ],
    form: {
        ...inclds('users.view', 'users.invite', 'users.edit', 'users.delete')
    }
}

const roles = {
    arr: [
        { name: 'Pogled', id: 'roles.view'},
        { name: 'Ustvarjanje', id: 'roles.create'},
        { name: 'Urejanje', id: 'roles.edit'},
        { name: 'Brisanje', id: 'roles.delete'}
    ],
    form: {
        ...inclds('roles.view', 'roles.create', 'roles.edit', 'roles.delete')
    }
}

const classes = {
    arr: [
        { name: 'Pogled', id: 'classes.view'},
        { name: 'Ustvarjanje', id: 'classes.create'},
        { name: 'Urejanje', id: 'classes.edit'},
        { name: 'Brisanje', id: 'classes.delete'}
    ],
    form: {
        ...inclds('classes.view', 'classes.create', 'classes.edit', 'classes.delete')
    }
}

const subjects = {
    arr: [
        { name: 'Pogled', id: 'subjects.view'},
        { name: 'Ustvarjanje', id: 'subjects.create'},
        { name: 'Urejanje', id: 'subjects.edit'},
        { name: 'Brisanje', id: 'subjects.delete'}
    ],
    form: {
        ...inclds('subjects.view', 'subjects.create', 'subjects.edit', 'subjects.delete')
    }
}
</script>

<template>
    <div class="col-span-6 sm:col-span-4">
        <h1 class="mb-4 font-semibold text-gray-900 text-xl">Pravice</h1>
        <table class="w-full text-sm text-left text-gray-500">
            <tr class="bg-white hover:bg-gray-50">
                <div class="m-2">
                    <InputLabel for="admin.panel.view" textSize="text-base">
                        <div class="flex items-center">
                            <Checkbox class="p-2" id="admin.panel.view" v-model:checked="form['admin.panel.view']" name="admin.panel.view" required @change="emitter"/>
                            <div class="ml-2">
                                Admin panel
                            </div>
                        </div>
                    </InputLabel>
                </div>
            </tr>
            <div :class="[form['admin.panel.view'] ? '' : 'pointer-events-none opacity-30']" class="border p-2 rounded-md shadow mb-3 ml-5">
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
                <h1 class="font-semibold text-gray-900 text-lg m-2">Classes</h1>
                <table class="w-full">
                    <tr class="bg-white hover:bg-gray-50">
                        <div class="m-2 text-xl">
                            <InlineCheckboxes :items="classes.arr" :form="classes.form" @formChange="updateForm"/>
                        </div>
                    </tr>
                </table>
                <h1 class="font-semibold text-gray-900 text-lg m-2">Subjects</h1>
                <table class="w-full">
                    <tr class="bg-white hover:bg-gray-50">
                        <div class="m-2 text-xl">
                            <InlineCheckboxes :items="subjects.arr" :form="subjects.form" @formChange="updateForm"/>
                        </div>
                    </tr>
                </table>
            </div>
            <tr class="bg-white hover:bg-gray-50">
                <div class="m-2">
                    <InputLabel for="class.teacher" textSize="text-base">
                        <div class="flex items-center">
                            <Checkbox class="p-2" id="class.teacher" v-model:checked="form['class.teacher']" name="class.teacher" required @change="emitter"/>
                            <div class="ml-2">
                                Je lahko razrednik
                            </div>
                        </div>
                    </InputLabel>
                </div>
            </tr>
            <tr class="bg-white hover:bg-gray-50">
                <div class="m-2">
                    <InputLabel for="teacher" textSize="text-base">
                        <div class="flex items-center">
                            <Checkbox class="p-2" id="teacher" v-model:checked="form['teacher']" name="teacher" required @change="emitter"/>
                            <div class="ml-2">
                                Je lahko nosilec predmeta
                            </div>
                        </div>
                    </InputLabel>
                </div>
            </tr>
        </table>
    </div>
</template>