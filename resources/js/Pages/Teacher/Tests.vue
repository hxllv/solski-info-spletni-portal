<script setup>
import TeacherLayout from "@/Layouts/TeacherLayout.vue";
import Select from "@/Components/Select.vue";
import Tests from "@/Components/Tests.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import DateInput from "@/Components/DateInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import DialogModal from "@/Components/DialogModal.vue";
import { Link, useForm } from "@inertiajs/vue3";
import { ref, watch } from "vue";

const props = defineProps({
    subjects: Array,
    testsInFuture: Object,
    testsInPast: Object,
    students: Array,
    teachersSubjectIds: Array,
    teachersSubjects: Array,
    classId: String,
    classes: Array,
    permission: Array,
});

const addingTest = ref(false);
const formAdd = useForm({
    date: new Date().toISOString().split("T")[0],
    subject: "",
    note: "",
});

// adding

const closeAddingModal = () => {
    addingTest.value = false;
};

const openAddingModal = () => {
    addingTest.value = true;
};

const addAbsences = () => {
    formAdd
        .transform(() => ({
            ...formAdd,
            classId: props.classId,
        }))
        .post(route("create.tests"), {
            preserveScroll: true,
            onSuccess: () => closeAddingModal(),
            onError: () => {},
        });
};
</script>

<template>
    <TeacherLayout
        title="Ocenjevanja"
        :permission="permission"
        :classes="classes"
        :classId="classId"
        v-slot="layout"
    >
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="pb-5 md:grid md:grid-cols-4 md:gap-2">
                <div class="mt-5 md:mt-0 md:col-span-1">
                    <PrimaryButton @click="openAddingModal">
                        Dodaj ocenjevanje
                    </PrimaryButton>
                </div>
            </div>

            <div class="py-4 font-medium">Prihodnja ocenjevanja</div>

            <Tests
                :tests="testsInFuture"
                :allowEdit="permission.includes('absences.edit')"
                :allowDelete="permission.includes('absences.delete')"
                :allowActionsFor="teachersSubjectIds"
                :buffer="layout.buffer"
            />

            <div class="py-4 font-medium">Pretekla ocenjevanja</div>

            <Tests
                :tests="testsInPast"
                :allowEdit="permission.includes('absences.edit')"
                :allowDelete="permission.includes('absences.delete')"
                :allowActionsFor="teachersSubjectIds"
                :buffer="layout.buffer"
            />
        </div>

        <DialogModal :show="addingTest" @close="closeAddingModal">
            <template #title> Dodaj ocenjevanje </template>
            <template #content>
                <form @submit.prevent="">
                    <div class="px-4 py-5 bg-white sm:p-6">
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-4">
                                <InputLabel for="date" value="Datum" />
                                <DateInput
                                    id="date"
                                    class="mt-1 block w-full"
                                    v-model="formAdd.date"
                                />
                            </div>
                            <div class="col-span-6 sm:col-span-4">
                                <InputLabel for="subject" value="Predmet" />
                                <Select
                                    id="subject"
                                    v-model="formAdd.subject"
                                    class="mt-1 block w-full"
                                >
                                    <option
                                        v-for="subject in teachersSubjects"
                                        :value="subject.id"
                                    >
                                        {{ subject.name }} -
                                        {{ subject.users_full_name }}
                                    </option>
                                </Select>
                                <InputError
                                    :message="formAdd.errors.hour"
                                    class="mt-2"
                                />
                            </div>
                            <div class="col-span-6 sm:col-span-4">
                                <InputLabel for="note" value="Opombe" />
                                <TextInput
                                    id="note"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="formAdd.note"
                                />
                                <InputError
                                    :message="formAdd.errors.note"
                                    class="mt-2"
                                />
                            </div>
                        </div>
                    </div>
                </form>
            </template>

            <template #footer>
                <SecondaryButton @click="closeAddingModal">
                    Prekliči
                </SecondaryButton>

                <PrimaryButton class="ml-3" @click="addAbsences">
                    Shrani
                </PrimaryButton>
            </template>
        </DialogModal>
    </TeacherLayout>
</template>
