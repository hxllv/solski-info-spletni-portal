<script setup>
import TeacherLayout from "@/Layouts/TeacherLayout.vue";
import Select from "@/Components/Select.vue";
import Overrides from "@/Components/Overrides.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import DateInput from "@/Components/DateInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import DialogModal from "@/Components/DialogModal.vue";
import { Link, useForm, router } from "@inertiajs/vue3";
import { ref, watch } from "vue";

const props = defineProps({
    todaysEntries: Array,
    allSubjects: Array,
    allTimetableEntries: Object,
    overridesInFuture: Object,
    overridesInPast: Object,
    classTeacherClassIds: Array,
    classId: String,
    forDate: String,
    classes: Array,
    permission: Array,
});

const forDateVal = ref(props.forDate);

const addingOverride = ref(false);
const formAdd = useForm({
    subject: "",
    note: "",
    hour: "",
});

watch(forDateVal, (newDate) => {
    router.get(
        route("overrides"),
        {
            classId: props.classId,
            forDate: newDate,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
});

// adding

const closeAddingModal = () => {
    addingOverride.value = false;
    forDateVal.value = new Date().toISOString().split("T")[0];

    formAdd.reset();
};

const openAddingModal = () => {
    addingOverride.value = true;
};

const addOverride = () => {
    formAdd
        .transform(() => ({
            ...formAdd,
            classId: props.classId,
            date: forDateVal.value,
        }))
        .post(route("create.overrides"), {
            preserveScroll: true,
            onSuccess: () => closeAddingModal(),
            onError: () => {},
        });
};

console.log(props);
</script>

<template>
    <TeacherLayout
        title="Nadomeščanja"
        :permission="permission"
        :classes="classes"
        :classId="classId"
        v-slot="layout"
    >
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div
                class="pb-5 md:grid md:grid-cols-4 md:gap-2"
                v-if="
                    permission.includes('timetable.override.create') &&
                    classTeacherClassIds.includes(Number(classId))
                "
            >
                <div class="mt-5 md:mt-0 md:col-span-1">
                    <PrimaryButton @click="openAddingModal">
                        Dodaj nadomeščanje
                    </PrimaryButton>
                </div>
            </div>

            <div class="py-4 font-medium">Prihodnja nadomeščanja</div>

            <Overrides
                :overrides="overridesInFuture"
                :entries="allTimetableEntries"
                :allowEdit="permission.includes('timetable.override.edit')"
                :allowDelete="permission.includes('timetable.override.delete')"
                :buffer="layout.buffer"
            />

            <div class="py-4 font-medium">Pretekla nadomeščanja</div>

            <Overrides
                :overrides="overridesInPast"
                :entries="allTimetableEntries"
                :allowEdit="permission.includes('timetable.override.edit')"
                :allowDelete="permission.includes('timetable.override.delete')"
                :buffer="layout.buffer"
            />
        </div>

        <DialogModal
            :show="addingOverride"
            @close="closeAddingModal"
            maxWidth="4xl"
        >
            <template #title> Dodaj nadomeščanje </template>
            <template #content>
                <form @submit.prevent="">
                    <div class="px-4 py-5 bg-white sm:p-6">
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-4">
                                <InputLabel for="date" value="Datum" />
                                <DateInput
                                    id="date"
                                    class="mt-1 block w-full"
                                    v-model="forDateVal"
                                />
                            </div>
                            <!-- Hour -->
                            <div class="col-span-6 sm:col-span-4">
                                <InputLabel for="hour" value="Ura" />
                                <Select
                                    id="hour"
                                    v-model="formAdd.hour"
                                    class="mt-1 block w-full"
                                >
                                    <option
                                        v-for="today in todaysEntries"
                                        :value="today.hour"
                                    >
                                        {{ today.subject_teacher.name }} -
                                        {{
                                            today.subject_teacher
                                                .users_full_name
                                        }}
                                        -
                                        {{ today.hour_name }}
                                    </option>
                                </Select>
                                <InputError
                                    :message="formAdd.errors.hour"
                                    class="mt-2"
                                />
                            </div>
                            <!-- Subject -->
                            <div class="col-span-6 sm:col-span-4">
                                <InputLabel
                                    for="subject"
                                    value="Nadomestni predmet"
                                />
                                <Select
                                    id="subject"
                                    v-model="formAdd.subject"
                                    class="mt-1 block w-full"
                                >
                                    <option value="-1">/</option>
                                    <option
                                        v-for="subject in allSubjects"
                                        :value="subject.id"
                                    >
                                        {{ subject.name }} -
                                        {{ subject.users_full_name }}
                                    </option>
                                </Select>
                                <InputError
                                    :message="formAdd.errors.subject"
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
                <InputError :message="formAdd.errors.extra" class="mt-2" />
                <SecondaryButton @click="closeAddingModal">
                    Prekliči
                </SecondaryButton>

                <PrimaryButton class="ml-3" @click="addOverride">
                    Shrani
                </PrimaryButton>
            </template>
        </DialogModal>
    </TeacherLayout>
</template>
