<script setup>
import { ref, computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import Spinner from "@/Components/Spinner.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import DialogModal from "@/Components/DialogModal.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import DateInput from "@/Components/DateInput.vue";

const props = defineProps({
    tests: Object,
    allowEdit: {
        type: Boolean,
        default: false,
    },
    allowDelete: {
        type: Boolean,
        default: false,
    },
    allowActionsFor: {
        type: Array,
        default: [],
    },
    buffer: Boolean,
});

const testInfo = ref(false);
const selectedTest = ref(false);

const confirmingTestDeletion = ref(false);
const testEditing = ref(false);

const formDelete = useForm({});
const formEdit = useForm({
    date: "",
    note: "",
});

// info

const closeInfoModal = () => {
    testInfo.value = false;
    selectedTest.value = false;
};

const openInfoModal = (test) => {
    testInfo.value = true;
    selectedTest.value = test;
};

// deleting

const closeDeleteModal = (fullClose = false) => {
    testInfo.value = true;
    confirmingTestDeletion.value = false;

    if (fullClose) closeInfoModal();
};

const openDeleteModal = () => {
    testInfo.value = false;
    confirmingTestDeletion.value = true;
};

const deleteTest = () => {
    formDelete.delete(route("tests.delete", selectedTest.value.id), {
        preserveScroll: true,
        onSuccess: () => closeDeleteModal(true),
        onError: () => {},
    });
};

// editing

const closeEditModal = (fullClose = false) => {
    testInfo.value = true;
    testEditing.value = false;

    formEdit.reset();

    if (fullClose) closeInfoModal();
};

const openEditModal = () => {
    testInfo.value = false;
    testEditing.value = true;

    formEdit.date = selectedTest.value.date;
    formEdit.note = selectedTest.value.note;
};

const editTest = () => {
    formEdit.put(route("tests.update", selectedTest.value.id), {
        preserveScroll: true,
        onSuccess: () => closeEditModal(true),
    });
};
</script>

<template>
    <div class="relative">
        <Spinner :buffer="buffer" />

        <div class="overflow-x-auto grid grid-cols-3 sm:rounded-md shadow">
            <table class="table-fixed mt-0 col-span-3">
                <thead>
                    <tr
                        class="text-xs text-left text-gray-700 uppercase bg-gray-50 grid grid-cols-4"
                    >
                        <th class="px-6 py-3 border-r col-span-1">Datum</th>
                        <th class="px-6 py-3 border-r col-span-3">Predmet</th>
                    </tr>
                </thead>
                <tbody class="text-xs md:text-sm lg:text-base">
                    <tr
                        v-for="(test, key) in tests"
                        class="bg-white border-b hover:bg-gray-100 grid grid-cols-4"
                    >
                        <td class="p-3 border-r text-center col-span-1">
                            {{
                                new Intl.DateTimeFormat("sl-SI", {
                                    dateStyle: "long",
                                }).format(new Date(key))
                            }}
                        </td>
                        <td class="px-6 py-3 border-r col-span-3">
                            <div v-for="testy in test">
                                <span
                                    class="cursor-pointer"
                                    @click="openInfoModal(testy)"
                                >
                                    {{ testy.subject_teacher.name }}
                                </span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <DialogModal :show="testInfo" @close="closeInfoModal">
        <template #content>
            <ul class="list-disc py-2 pl-4">
                <li>Predmet: {{ selectedTest.subject_teacher.name }}</li>
                <li>
                    Nosilec: {{ selectedTest.subject_teacher.users_full_name }}
                </li>
                <li v-if="selectedTest.note">
                    Opombe: {{ selectedTest.note }}
                </li>
            </ul>
        </template>

        <template #footer>
            <DangerButton
                v-if="
                    allowDelete &&
                    allowActionsFor.includes(selectedTest.subject_teacher_id)
                "
                @click="openDeleteModal"
            >
                Izbriši
            </DangerButton>
            <PrimaryButton
                v-if="
                    allowEdit &&
                    allowActionsFor.includes(selectedTest.subject_teacher_id)
                "
                class="ml-3"
                @click="openEditModal"
            >
                Uredi
            </PrimaryButton>
            <SecondaryButton class="ml-3" @click="closeInfoModal">
                Zapri
            </SecondaryButton>
        </template>
    </DialogModal>

    <DialogModal :show="confirmingTestDeletion" @close="closeDeleteModal">
        <template #title> Izbriši ocenjevanje? </template>

        <template #content>
            <InputError :message="formDelete.errors.delete" class="mt-2" />
        </template>

        <template #footer>
            <SecondaryButton @click="closeDeleteModal">
                Prekliči
            </SecondaryButton>

            <DangerButton class="ml-3" @click="deleteTest">
                Izbriši ocenjevanje
            </DangerButton>
        </template>
    </DialogModal>

    <DialogModal :show="testEditing" @close="closeEditModal">
        <template #title> Uredi ocenjevanje </template>

        <template #content>
            <form @submit.prevent="">
                <div class="px-4 py-5 bg-white sm:p-6">
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6 sm:col-span-4">
                            <InputLabel for="date" value="Datum" />
                            <DateInput
                                id="date"
                                class="mt-1 block w-full"
                                v-model="formEdit.date"
                            />
                        </div>
                        <div class="col-span-6 sm:col-span-4">
                            <InputLabel for="note" value="Opombe" />
                            <TextInput
                                id="note"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="formEdit.note"
                            />
                            <InputError
                                :message="formEdit.errors.note"
                                class="mt-2"
                            />
                        </div>
                    </div>
                </div>
            </form>
        </template>

        <template #footer>
            <SecondaryButton @click="closeEditModal">
                Prekliči
            </SecondaryButton>

            <PrimaryButton class="ml-3" @click="editTest">
                Shrani
            </PrimaryButton>
        </template>
    </DialogModal>
</template>
