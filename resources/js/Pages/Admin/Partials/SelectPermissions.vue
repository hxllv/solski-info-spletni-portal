<script setup>
import InlineCheckboxes from "@/Components/InlineCheckboxes.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Checkbox from "@/Components/Checkbox.vue";
import { ref, computed } from "vue";

const emit = defineEmits(["formChange"]);

const props = defineProps({
    data: Array,
});

const inclds = (...names) => {
    let newData = {};

    if (!names.length) {
        props.data.forEach((dat) => {
            newData[dat] = true;
        });

        return newData;
    }

    names.forEach((name) => {
        newData[name] = false;
        if (props.data.includes(name)) newData[name] = true;
    });

    return newData;
};

let form = ref({
    "teacher.panel.view": false,
    "admin.panel.view": false,
    "dashboard.view": false,
    ...inclds(),
});

const updateForm = (newForm) => {
    form.value = {
        ...form.value,
        ...newForm,
    };

    emitter();
};

const emitter = () => {
    emit("formChange", form.value);
};

// dashboard

const timetableDash = {
    arr: [{ name: "Pogled", id: "dashboard.timetable.view" }],
    form: {
        ...inclds("dashboard.timetable.view"),
    },
};

const absencesDash = {
    arr: [
        { name: "Pogled", id: "dashboard.absences.view" },
        { name: "Opravičila", id: "dashboard.absences.edit" },
    ],
    form: {
        ...inclds("dashboard.absences.view", "dashboard.absences.edit"),
    },
};

const gradebookDash = {
    arr: [{ name: "Pogled", id: "dashboard.gradebook.view" }],
    form: {
        ...inclds("dashboard.gradebook.view"),
    },
};

// teacher panel

const gradebook = {
    arr: [
        { name: "Pogled", id: "gradebook.view" },
        { name: "Vnos", id: "gradebook.create" },
        { name: "Urejanje", id: "gradebook.edit" },
        { name: "Brisanje", id: "gradebook.delete" },
    ],
    form: {
        ...inclds(
            "gradebook.view",
            "gradebook.create",
            "gradebook.edit",
            "gradebook.delete"
        ),
    },
};

const gradebookExtra = {
    arr: [{ name: "Brez omejitve predmetov", id: "gradebook.bypass" }],
    form: {
        ...inclds("gradebook.bypass"),
    },
};

const absences = {
    arr: [
        { name: "Pogled", id: "absences.view" },
        { name: "Vnos", id: "absences.create" },
        { name: "Urejanje", id: "absences.edit" },
        { name: "Brisanje", id: "absences.delete" },
    ],
    form: {
        ...inclds(
            "absences.view",
            "absences.create",
            "absences.edit",
            "absences.delete"
        ),
    },
};

const absencesExtra = {
    arr: [{ name: "Odobritev/zavrnitev", id: "absences.approval" }],
    form: {
        ...inclds("absences.approval"),
    },
};

const absencesExtra1 = {
    arr: [{ name: "Brez omejitve predmetov", id: "absences.bypass.subject" }],
    form: {
        ...inclds("absences.bypass.subject"),
    },
};

const absencesExtra2 = {
    arr: [{ name: "Brez omejitve razredov", id: "absences.bypass.class" }],
    form: {
        ...inclds("absences.bypass.class"),
    },
};

const tests = {
    arr: [
        { name: "Pogled", id: "tests.view" },
        { name: "Vnos", id: "tests.create" },
        { name: "Urejanje", id: "tests.edit" },
        { name: "Brisanje", id: "tests.delete" },
    ],
    form: {
        ...inclds("tests.view", "tests.create", "tests.edit", "tests.delete"),
    },
};

const testsExtra = {
    arr: [{ name: "Brez omejitve predmetov", id: "tests.bypass" }],
    form: {
        ...inclds("tests.bypass"),
    },
};

const overrides = {
    arr: [
        { name: "Pogled", id: "timetable.override.view" },
        { name: "Vnos", id: "timetable.override.create" },
        { name: "Urejanje", id: "timetable.override.edit" },
        { name: "Brisanje", id: "timetable.override.delete" },
    ],
    form: {
        ...inclds(
            "timetable.override.view",
            "timetable.override.create",
            "timetable.override.edit",
            "timetable.override.delete"
        ),
    },
};

const overridesExtra = {
    arr: [
        {
            name: "Brez omejitve razredov",
            id: "timetable.override.bypass",
        },
    ],
    form: {
        ...inclds("timetable.override.bypass"),
    },
};

// admin panel

const users = {
    arr: [
        { name: "Pogled", id: "users.view" },
        { name: "Povabljanje", id: "users.invite" },
        { name: "Urejanje", id: "users.edit" },
        { name: "Brisanje", id: "users.delete" },
    ],
    form: {
        ...inclds("users.view", "users.invite", "users.edit", "users.delete"),
    },
};

const roles = {
    arr: [
        { name: "Pogled", id: "roles.view" },
        { name: "Ustvarjanje", id: "roles.create" },
        { name: "Urejanje", id: "roles.edit" },
        { name: "Brisanje", id: "roles.delete" },
    ],
    form: {
        ...inclds("roles.view", "roles.create", "roles.edit", "roles.delete"),
    },
};

const classes = {
    arr: [
        { name: "Pogled", id: "classes.view" },
        { name: "Ustvarjanje", id: "classes.create" },
        { name: "Urejanje", id: "classes.edit" },
        { name: "Brisanje", id: "classes.delete" },
    ],
    form: {
        ...inclds(
            "classes.view",
            "classes.create",
            "classes.edit",
            "classes.delete"
        ),
    },
};

const subjects = {
    arr: [
        { name: "Pogled", id: "subjects.view" },
        { name: "Ustvarjanje", id: "subjects.create" },
        { name: "Urejanje", id: "subjects.edit" },
        { name: "Brisanje", id: "subjects.delete" },
    ],
    form: {
        ...inclds(
            "subjects.view",
            "subjects.create",
            "subjects.edit",
            "subjects.delete"
        ),
    },
};

const timetable = {
    arr: [{ name: "Urejanje", id: "timetable.edit" }],
    form: {
        ...inclds("timetable.edit"),
    },
};

const settings = {
    arr: [
        { name: "Pogled", id: "settings.view" },
        { name: "Urejanje", id: "settings.edit" },
    ],
    form: {
        ...inclds("settings.edit", "settings.view"),
    },
};
</script>

<template>
    <div class="col-span-6 sm:col-span-4">
        <h1 class="mb-4 font-semibold text-gray-900 text-xl">Pravice</h1>
        <table class="w-full text-sm text-left text-gray-500">
            <tr class="bg-white hover:bg-gray-50">
                <div class="m-2">
                    <InputLabel for="dashboard" textSize="text-base">
                        <div class="flex items-center">
                            <Checkbox
                                class="p-2"
                                id="dashboard"
                                v-model:checked="form['dashboard.view']"
                                name="dashboard"
                                required
                                @change="emitter"
                            />
                            <div class="ml-2">Namizje</div>
                        </div>
                    </InputLabel>
                </div>
            </tr>
            <div
                :class="[
                    form['dashboard.view']
                        ? ''
                        : 'pointer-events-none opacity-30',
                ]"
                class="border p-2 rounded-md shadow mb-3 ml-5"
            >
                <table class="w-full">
                    <tr class="bg-white hover:bg-gray-50">
                        <div class="m-2">
                            <InputLabel
                                for="dashboard.me.view"
                                textSize="text-base"
                            >
                                <div class="flex items-center">
                                    <Checkbox
                                        class="p-2"
                                        id="dashboard.me.view"
                                        v-model:checked="
                                            form['dashboard.me.view']
                                        "
                                        name="dashboard.me.view"
                                        required
                                        @change="emitter"
                                    />
                                    <div class="ml-2">Vpogled zase</div>
                                </div>
                            </InputLabel>
                        </div>
                    </tr>
                </table>
                <table class="w-full">
                    <tr class="bg-white hover:bg-gray-50">
                        <div class="m-2">
                            <InputLabel
                                for="dashboard.children.view"
                                textSize="text-base"
                            >
                                <div class="flex items-center">
                                    <Checkbox
                                        class="p-2"
                                        id="dashboard.children.view"
                                        v-model:checked="
                                            form['dashboard.children.view']
                                        "
                                        name="dashboard.children.view"
                                        required
                                        @change="emitter"
                                    />
                                    <div class="ml-2">Vpogled za otroke</div>
                                </div>
                            </InputLabel>
                        </div>
                    </tr>
                </table>
                <table class="w-full">
                    <tr class="bg-white hover:bg-gray-50">
                        <div class="m-2">
                            <InputLabel
                                for="dashboard.all.view"
                                textSize="text-base"
                            >
                                <div class="flex items-center">
                                    <Checkbox
                                        class="p-2"
                                        id="dashboard.all.view"
                                        v-model:checked="
                                            form['dashboard.all.view']
                                        "
                                        name="dashboard.all.view"
                                        required
                                        @change="emitter"
                                    />
                                    <div class="ml-2">Vpogled za vse</div>
                                </div>
                            </InputLabel>
                        </div>
                    </tr>
                </table>
                <h1 class="font-semibold text-gray-900 text-lg m-2">Urnik</h1>
                <table class="w-full">
                    <tr class="bg-white hover:bg-gray-50">
                        <div class="m-2 text-xl">
                            <InlineCheckboxes
                                :items="timetableDash.arr"
                                :form="timetableDash.form"
                                @formChange="updateForm"
                            />
                        </div>
                    </tr>
                </table>
                <h1 class="font-semibold text-gray-900 text-lg m-2">
                    Redovalnica
                </h1>
                <table class="w-full">
                    <tr class="bg-white hover:bg-gray-50">
                        <div class="m-2 text-xl">
                            <InlineCheckboxes
                                :items="gradebookDash.arr"
                                :form="gradebookDash.form"
                                @formChange="updateForm"
                            />
                        </div>
                    </tr>
                </table>
                <h1 class="font-semibold text-gray-900 text-lg m-2">
                    Izostanki
                </h1>
                <table class="w-full">
                    <tr class="bg-white hover:bg-gray-50">
                        <div class="m-2 text-xl">
                            <InlineCheckboxes
                                :items="absencesDash.arr"
                                :form="absencesDash.form"
                                @formChange="updateForm"
                            />
                        </div>
                    </tr>
                </table>
            </div>
            <tr class="bg-white hover:bg-gray-50">
                <div class="m-2">
                    <InputLabel for="teacher" textSize="text-base">
                        <div class="flex items-center">
                            <Checkbox
                                class="p-2"
                                id="teacher"
                                v-model:checked="form['teacher.panel.view']"
                                name="teacher"
                                required
                                @change="emitter"
                            />
                            <div class="ml-2">Učiteljski panel</div>
                        </div>
                    </InputLabel>
                </div>
            </tr>
            <div
                :class="[
                    form['teacher.panel.view']
                        ? ''
                        : 'pointer-events-none opacity-30',
                ]"
                class="border p-2 rounded-md shadow mb-3 ml-5"
            >
                <table class="w-full">
                    <tr class="bg-white hover:bg-gray-50">
                        <div class="m-2">
                            <InputLabel
                                for="class.teacher"
                                textSize="text-base"
                            >
                                <div class="flex items-center">
                                    <Checkbox
                                        class="p-2"
                                        id="class.teacher"
                                        v-model:checked="form['class.teacher']"
                                        name="class.teacher"
                                        required
                                        @change="emitter"
                                    />
                                    <div class="ml-2">Je lahko razrednik</div>
                                </div>
                            </InputLabel>
                        </div>
                    </tr>
                </table>
                <h1 class="font-semibold text-gray-900 text-lg m-2">
                    Redovalnica
                </h1>
                <table class="w-full">
                    <tr class="bg-white hover:bg-gray-50">
                        <div class="m-2 text-xl">
                            <InlineCheckboxes
                                :items="gradebook.arr"
                                :form="gradebook.form"
                                @formChange="updateForm"
                            />
                        </div>
                    </tr>
                    <tr class="bg-white hover:bg-gray-50">
                        <div class="m-2 text-xl">
                            <InlineCheckboxes
                                :items="gradebookExtra.arr"
                                :form="gradebookExtra.form"
                                @formChange="updateForm"
                            />
                        </div>
                    </tr>
                </table>
                <h1 class="font-semibold text-gray-900 text-lg m-2">
                    Izostanki
                </h1>
                <table class="w-full">
                    <tr class="bg-white hover:bg-gray-50">
                        <div class="m-2 text-xl">
                            <InlineCheckboxes
                                :items="absences.arr"
                                :form="absences.form"
                                @formChange="updateForm"
                            />
                        </div>
                    </tr>
                    <tr class="bg-white hover:bg-gray-50">
                        <div class="m-2 text-xl">
                            <InlineCheckboxes
                                :items="absencesExtra.arr"
                                :form="absencesExtra.form"
                                @formChange="updateForm"
                            />
                        </div>
                    </tr>
                    <tr class="bg-white hover:bg-gray-50">
                        <div class="m-2 text-xl">
                            <InlineCheckboxes
                                :items="absencesExtra1.arr"
                                :form="absencesExtra1.form"
                                @formChange="updateForm"
                            />
                        </div>
                    </tr>
                    <tr class="bg-white hover:bg-gray-50">
                        <div class="m-2 text-xl">
                            <InlineCheckboxes
                                :items="absencesExtra2.arr"
                                :form="absencesExtra2.form"
                                @formChange="updateForm"
                            />
                        </div>
                    </tr>
                </table>
                <h1 class="font-semibold text-gray-900 text-lg m-2">
                    Datumi ocenjevanja
                </h1>
                <table class="w-full">
                    <tr class="bg-white hover:bg-gray-50">
                        <div class="m-2 text-xl">
                            <InlineCheckboxes
                                :items="tests.arr"
                                :form="tests.form"
                                @formChange="updateForm"
                            />
                        </div>
                    </tr>
                    <tr class="bg-white hover:bg-gray-50">
                        <div class="m-2 text-xl">
                            <InlineCheckboxes
                                :items="testsExtra.arr"
                                :form="testsExtra.form"
                                @formChange="updateForm"
                            />
                        </div>
                    </tr>
                </table>
                <h1 class="font-semibold text-gray-900 text-lg m-2">
                    Nadomeščanja
                </h1>
                <table class="w-full">
                    <tr class="bg-white hover:bg-gray-50">
                        <div class="m-2 text-xl">
                            <InlineCheckboxes
                                :items="overrides.arr"
                                :form="overrides.form"
                                @formChange="updateForm"
                            />
                        </div>
                    </tr>
                    <tr class="bg-white hover:bg-gray-50">
                        <div class="m-2 text-xl">
                            <InlineCheckboxes
                                :items="overridesExtra.arr"
                                :form="overridesExtra.form"
                                @formChange="updateForm"
                            />
                        </div>
                    </tr>
                </table>
            </div>
            <tr class="bg-white hover:bg-gray-50">
                <div class="m-2">
                    <InputLabel for="admin.panel.view" textSize="text-base">
                        <div class="flex items-center">
                            <Checkbox
                                class="p-2"
                                id="admin.panel.view"
                                v-model:checked="form['admin.panel.view']"
                                name="admin.panel.view"
                                required
                                @change="emitter"
                            />
                            <div class="ml-2">Administratorski panel</div>
                        </div>
                    </InputLabel>
                </div>
            </tr>
            <div
                :class="[
                    form['admin.panel.view']
                        ? ''
                        : 'pointer-events-none opacity-30',
                ]"
                class="border p-2 rounded-md shadow mb-3 ml-5"
            >
                <h1 class="font-semibold text-gray-900 text-lg m-2">
                    Uporabniki
                </h1>
                <table class="w-full">
                    <tr class="bg-white hover:bg-gray-50">
                        <div class="m-2 text-xl">
                            <InlineCheckboxes
                                :items="users.arr"
                                :form="users.form"
                                @formChange="updateForm"
                            />
                        </div>
                    </tr>
                </table>
                <h1 class="font-semibold text-gray-900 text-lg m-2">
                    Skupine pravic
                </h1>
                <table class="w-full">
                    <tr class="bg-white hover:bg-gray-50">
                        <div class="m-2 text-xl">
                            <InlineCheckboxes
                                :items="roles.arr"
                                :form="roles.form"
                                @formChange="updateForm"
                            />
                        </div>
                    </tr>
                </table>
                <h1 class="font-semibold text-gray-900 text-lg m-2">Razredi</h1>
                <table class="w-full">
                    <tr class="bg-white hover:bg-gray-50">
                        <div class="m-2 text-xl">
                            <InlineCheckboxes
                                :items="classes.arr"
                                :form="classes.form"
                                @formChange="updateForm"
                            />
                        </div>
                    </tr>
                </table>
                <h1 class="font-semibold text-gray-900 text-lg m-2">
                    Predmeti
                </h1>
                <table class="w-full">
                    <tr class="bg-white hover:bg-gray-50">
                        <div class="m-2 text-xl">
                            <InlineCheckboxes
                                :items="subjects.arr"
                                :form="subjects.form"
                                @formChange="updateForm"
                            />
                        </div>
                    </tr>
                </table>
                <h1 class="font-semibold text-gray-900 text-lg m-2">Urnik</h1>
                <table class="w-full">
                    <tr class="bg-white hover:bg-gray-50">
                        <div class="m-2 text-xl">
                            <InlineCheckboxes
                                :items="timetable.arr"
                                :form="timetable.form"
                                @formChange="updateForm"
                            />
                        </div>
                    </tr>
                </table>
                <h1 class="font-semibold text-gray-900 text-lg m-2">Ostalo</h1>
                <table class="w-full">
                    <tr class="bg-white hover:bg-gray-50">
                        <div class="m-2 text-xl">
                            <InlineCheckboxes
                                :items="settings.arr"
                                :form="settings.form"
                                @formChange="updateForm"
                            />
                        </div>
                    </tr>
                </table>
            </div>
        </table>
    </div>
</template>
