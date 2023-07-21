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
    'teacher.panel.view': false,
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
    'timetable.edit': false,
    'settings.edit': false,
    'settings.view': false,
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

const gradebook = {
    arr: [
        { name: 'Pogled', id: 'gradebook.view'},
        { name: 'Vnos', id: 'gradebook.create'},
        { name: 'Urejanje', id: 'gradebook.edit'},
        { name: 'Brisanje', id: 'gradebook.delete'}
    ],
    form: {
        ...inclds('gradebook.view', 'gradebook.create', 'gradebook.edit', 'gradebook.delete')
    }
}

const missing = {
    arr: [
        { name: 'Pogled', id: 'missing.view'},
        { name: 'Vnos', id: 'missing.create'},
        { name: 'Urejanje', id: 'missing.edit'},
        { name: 'Brisanje', id: 'missing.delete'}
    ],
    form: {
        ...inclds('missing.view', 'missing.create', 'missing.edit', 'missing.delete')
    }
}

const test = {
    arr: [
        { name: 'Pogled', id: 'test.view'},
        { name: 'Vnos', id: 'test.create'},
        { name: 'Urejanje', id: 'test.edit'},
        { name: 'Brisanje', id: 'test.delete'}
    ],
    form: {
        ...inclds('test.view', 'test.create', 'test.edit', 'test.delete')
    }
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

const timetable = {
    arr: [
        { name: 'Urejanje', id: 'timetable.edit'},
    ],
    form: {
        ...inclds('timetable.edit')
    }
}

const settings = {
    arr: [
        { name: 'Pogled', id: 'settings.view'},
        { name: 'Urejanje', id: 'settings.edit'},
    ],
    form: {
        ...inclds('settings.edit', 'settings.view')
    }
}
</script>

<template>
    <div class="col-span-6 sm:col-span-4">
        <h1 class="mb-4 font-semibold text-gray-900 text-xl">Pravice</h1>
        <table class="w-full text-sm text-left text-gray-500">
            <tr class="bg-white hover:bg-gray-50">
                <div class="m-2">
                    <InputLabel for="teacher" textSize="text-base">
                        <div class="flex items-center">
                            <Checkbox class="p-2" id="teacher" v-model:checked="form['teacher.panel.view']" name="teacher" required @change="emitter"/>
                            <div class="ml-2">
                                Učiteljski panel
                            </div>
                        </div>
                    </InputLabel>
                </div>
            </tr>
            <div :class="[form['teacher.panel.view'] ? '' : 'pointer-events-none opacity-30']" class="border p-2 rounded-md shadow mb-3 ml-5">
                <table class="w-full">
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
                </table>
                <table class="w-full">
                    <tr class="bg-white hover:bg-gray-50">
                        <div class="m-2">
                            <InputLabel for="all.classes.view" textSize="text-base">
                                <div class="flex items-center">
                                    <Checkbox class="p-2" id="all.classes.view" v-model:checked="form['all.classes.view']" name="all.classes.view" required @change="emitter"/>
                                    <div class="ml-2">
                                        Vpogled za vse razrede
                                    </div>
                                </div>
                            </InputLabel>
                        </div>
                    </tr>
                </table>
                <h1 class="font-semibold text-gray-900 text-lg m-2">Redovalnica</h1>
                <table class="w-full">
                    <tr class="bg-white hover:bg-gray-50">
                        <div class="m-2 text-xl">
                            <InlineCheckboxes :items="gradebook.arr" :form="gradebook.form" @formChange="updateForm"/>
                        </div>
                    </tr>
                </table>
                <h1 class="font-semibold text-gray-900 text-lg m-2">Izostanki</h1>
                <table class="w-full">
                    <tr class="bg-white hover:bg-gray-50">
                        <div class="m-2 text-xl">
                            <InlineCheckboxes :items="missing.arr" :form="missing.form" @formChange="updateForm"/>
                        </div>
                    </tr>
                </table>
                <h1 class="font-semibold text-gray-900 text-lg m-2">Datumi ocenjevanja</h1>
                <table class="w-full">
                    <tr class="bg-white hover:bg-gray-50">
                        <div class="m-2 text-xl">
                            <InlineCheckboxes :items="test.arr" :form="test.form" @formChange="updateForm"/>
                        </div>
                    </tr>
                </table>
            </div>
            <tr class="bg-white hover:bg-gray-50">
                <div class="m-2">
                    <InputLabel for="admin.panel.view" textSize="text-base">
                        <div class="flex items-center">
                            <Checkbox class="p-2" id="admin.panel.view" v-model:checked="form['admin.panel.view']" name="admin.panel.view" required @change="emitter"/>
                            <div class="ml-2">
                                Administratorski panel
                            </div>
                        </div>
                    </InputLabel>
                </div>
            </tr>
            <div :class="[form['admin.panel.view'] ? '' : 'pointer-events-none opacity-30']" class="border p-2 rounded-md shadow mb-3 ml-5">
                <h1 class="font-semibold text-gray-900 text-lg m-2">Uporabniki</h1>
                <table class="w-full">
                    <tr class="bg-white hover:bg-gray-50">
                        <div class="m-2 text-xl">
                            <InlineCheckboxes :items="users.arr" :form="users.form" @formChange="updateForm"/>
                        </div>
                    </tr>
                </table>
                <h1 class="font-semibold text-gray-900 text-lg m-2">Skupine pravic</h1>
                <table class="w-full">
                    <tr class="bg-white hover:bg-gray-50">
                        <div class="m-2 text-xl">
                            <InlineCheckboxes :items="roles.arr" :form="roles.form" @formChange="updateForm"/>
                        </div>
                    </tr>
                </table>
                <h1 class="font-semibold text-gray-900 text-lg m-2">Razredi</h1>
                <table class="w-full">
                    <tr class="bg-white hover:bg-gray-50">
                        <div class="m-2 text-xl">
                            <InlineCheckboxes :items="classes.arr" :form="classes.form" @formChange="updateForm"/>
                        </div>
                    </tr>
                </table>
                <h1 class="font-semibold text-gray-900 text-lg m-2">Predmeti</h1>
                <table class="w-full">
                    <tr class="bg-white hover:bg-gray-50">
                        <div class="m-2 text-xl">
                            <InlineCheckboxes :items="subjects.arr" :form="subjects.form" @formChange="updateForm"/>
                        </div>
                    </tr>
                </table>
                <h1 class="font-semibold text-gray-900 text-lg m-2">Urnik</h1>
                <table class="w-full">
                    <tr class="bg-white hover:bg-gray-50">
                        <div class="m-2 text-xl">
                            <InlineCheckboxes :items="timetable.arr" :form="timetable.form" @formChange="updateForm"/>
                        </div>
                    </tr>
                </table>
                <h1 class="font-semibold text-gray-900 text-lg m-2">Ostalo</h1>
                <table class="w-full">
                    <tr class="bg-white hover:bg-gray-50">
                        <div class="m-2 text-xl">
                            <InlineCheckboxes :items="settings.arr" :form="settings.form" @formChange="updateForm"/>
                        </div>
                    </tr>
                </table>
            </div>
        </table>
    </div>
</template>