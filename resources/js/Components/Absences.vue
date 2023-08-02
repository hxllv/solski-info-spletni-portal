<script setup>
import { ref, computed } from "vue";
import { router, useForm } from "@inertiajs/vue3";
import Spinner from "@/Components/Spinner.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import DialogModal from "@/Components/DialogModal.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import Select from "@/Components/Select.vue";

const props = defineProps({
    absences: Object,
    userId: String,
    classId: String,
    allowEdit: {
        type: Boolean,
        default: false,
    },
    allowDelete: {
        type: Boolean,
        default: false,
    },
    allowApproval: {
        type: Boolean,
        default: false,
    },
    allowActionsFor: {
        type: Array,
        default: [],
    },
    buffer: Boolean,
});

const absenceInfo = ref(false);
const selectedAbsence = ref(false);
const selectedDateAndStatus = ref(false);

const confirmingAbsenceDeletion = ref(false);
const absenceEditing = ref(false);

const formDelete = useForm({});
const formApproval = useForm({});
const formEdit = useForm({
    excuse: "",
});

// info

const shouldCloseInfoModal = () => {
    // if no absences left for date and status
    if (
        !props.absences[selectedDateAndStatus.value[0]] ||
        !props.absences[selectedDateAndStatus.value[0]].filter(
            (abs) => abs.status == selectedDateAndStatus[1]
        ).length
    )
        closeInfoModal();
};

const closeInfoModal = () => {
    absenceInfo.value = false;
    selectedAbsence.value = false;
    selectedDateAndStatus.value = false;
};

const openInfoModal = (date, status) => {
    absenceInfo.value = true;
    selectedDateAndStatus.value = [date, status];
};

// deleting

const closeDeleteModal = () => {
    absenceInfo.value = true;
    confirmingAbsenceDeletion.value = false;
    selectedAbsence.value = false;

    shouldCloseInfoModal();
};

const openDeleteModal = (absence) => {
    absenceInfo.value = false;
    confirmingAbsenceDeletion.value = true;
    selectedAbsence.value = absence;
};

const deleteAbsence = () => {
    formDelete.delete(route("absences.delete", selectedAbsence.value.id), {
        preserveScroll: true,
        onSuccess: () => closeDeleteModal(),
        onError: () => {},
    });
};

// editing

const closeEditModal = () => {
    absenceInfo.value = true;
    absenceEditing.value = false;
    selectedAbsence.value = false;

    formEdit.reset();
};

const openEditModal = (absence) => {
    absenceInfo.value = false;
    absenceEditing.value = true;
    selectedAbsence.value = absence;

    formEdit.excuse = absence.excuse;
};

const editAbsence = () => {
    formEdit
        .transform(() => ({
            ...formEdit,
            classId: props.classId,
        }))
        .put(route("absences.excuse", selectedAbsence.value.id), {
            preserveScroll: true,
            onSuccess: () => closeEditModal(),
        });
};

// approval

const approvalUpdate = (id, status) => {
    formApproval
        .transform(() => ({
            status: status,
            classId: props.classId,
        }))
        .put(route("absences.approval", id), {
            preserveScroll: true,
            onSuccess: () => shouldCloseInfoModal(),
        });
};
</script>

<template>
    <div class="relative">
        <Spinner :buffer="buffer" />

        <div
            class="overflow-x-auto grid grid-cols-3 sm:rounded-md shadow"
            v-if="absences"
        >
            <table class="table-fixed mt-0 col-span-3">
                <thead>
                    <tr
                        class="text-xs text-left text-gray-700 uppercase bg-gray-50 grid grid-cols-4 break-words"
                    >
                        <th class="px-2 md:px-6 py-3 border-r col-span-1">
                            Datum
                        </th>
                        <th class="px-2 md:px-6 py-3 border-r col-span-1">
                            Št. odprtih
                        </th>
                        <th class="px-2 md:px-6 py-3 border-r col-span-1">
                            Št. opravičenih
                        </th>
                        <th class="px-2 md:px-6 py-3 border-r col-span-1">
                            Št. neopravičenih
                        </th>
                    </tr>
                </thead>
                <tbody class="text-xs md:text-sm lg:text-base">
                    <tr
                        class="bg-white border-b hover:bg-gray-100 grid grid-cols-4"
                    >
                        <td class="p-3 border-r text-center col-span-1">
                            <div>Skupaj</div>
                        </td>
                        <td
                            class="px-6 py-3 border-r text-center col-span-1 text-yellow-600"
                        >
                            <span>{{
                                Object.values(absences)
                                    .flat()
                                    .filter((abs) => abs.status == 0).length
                            }}</span>
                        </td>
                        <td
                            class="px-6 py-3 border-r text-center col-span-1 text-green-700"
                        >
                            <span>{{
                                Object.values(absences)
                                    .flat()
                                    .filter((abs) => abs.status == 1).length
                            }}</span>
                        </td>
                        <td
                            class="px-6 py-3 border-r text-center col-span-1 text-rose-700"
                        >
                            <span>{{
                                Object.values(absences)
                                    .flat()
                                    .filter((abs) => abs.status == 2).length
                            }}</span>
                        </td>
                    </tr>
                    <tr
                        v-for="(absence, key) in absences"
                        class="bg-white border-b hover:bg-gray-100 grid grid-cols-4"
                    >
                        <td class="p-3 border-r text-center col-span-1">
                            <div>
                                {{
                                    new Intl.DateTimeFormat("sl-SI", {
                                        dateStyle: "long",
                                    }).format(new Date(key))
                                }}
                            </div>
                        </td>
                        <td
                            class="px-6 py-3 border-r text-center col-span-1 text-yellow-600"
                        >
                            <span
                                class="cursor-pointer"
                                @click="openInfoModal(key, 0)"
                            >
                                {{
                                    absence.filter((abs) => abs.status == 0)
                                        .length
                                }}
                            </span>
                        </td>
                        <td
                            class="px-6 py-3 border-r text-center col-span-1 text-green-700"
                        >
                            <span
                                class="cursor-pointer"
                                @click="openInfoModal(key, 1)"
                            >
                                {{
                                    absence.filter((abs) => abs.status == 1)
                                        .length
                                }}
                            </span>
                        </td>
                        <td
                            class="px-6 py-3 border-r text-center col-span-1 text-rose-700"
                        >
                            <span
                                class="cursor-pointer"
                                @click="openInfoModal(key, 2)"
                            >
                                {{
                                    absence.filter((abs) => abs.status == 2)
                                        .length
                                }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <DialogModal :show="absenceInfo" @close="closeInfoModal" maxWidth="4xl">
        <template #content>
            <div class="overflow-x-auto grid grid-cols-3 sm:rounded-md shadow">
                <table class="table-fixed mt-0 col-span-3">
                    <thead
                        class="text-xs text-left text-gray-700 uppercase bg-gray-50"
                    >
                        <tr>
                            <th scope="col" class="px-6 py-3 border-r">
                                Predmet
                            </th>
                            <th scope="col" class="px-6 py-3 border-r">Ura</th>
                            <th scope="col" class="px-6 py-3 border-r">
                                Opravičilo
                            </th>
                            <th
                                scope="col"
                                class="py-3 max-w-fit"
                                v-if="allowEdit || allowDelete"
                            ></th>
                        </tr>
                    </thead>
                    <tbody class="text-xs md:text-sm lg:text-base">
                        <tr
                            v-for="absence in absences[
                                selectedDateAndStatus[0]
                            ].filter(
                                (abs) => abs.status == selectedDateAndStatus[1]
                            )"
                            class="bg-white border-b hover:bg-gray-100"
                        >
                            <td class="px-6 py-4 border-r">
                                {{ absence.subject_teacher.name }} -
                                {{ absence.subject_teacher.users_full_name }}
                            </td>
                            <td class="px-6 py-4 border-r">
                                {{ absence.hour_name }}
                            </td>
                            <td class="px-6 py-4 border-r">
                                {{ absence.excuse }}
                            </td>
                            <td
                                class="text-right max-w-fit"
                                v-if="allowEdit || allowDelete"
                            >
                                <div class="flex justify-center px-1">
                                    <a
                                        class="cursor-pointer"
                                        @click="approvalUpdate(absence.id, 0)"
                                        v-if="allowApproval"
                                    >
                                        <font-awesome-icon
                                            icon="fa-solid fa-question"
                                            class="text-gray-700 px-2"
                                        />
                                    </a>
                                    <a
                                        class="cursor-pointer"
                                        @click="approvalUpdate(absence.id, 1)"
                                        v-if="allowApproval"
                                    >
                                        <font-awesome-icon
                                            icon="fa-solid fa-thumbs-up"
                                            class="text-gray-700 px-2"
                                        />
                                    </a>
                                    <a
                                        class="cursor-pointer"
                                        @click="approvalUpdate(absence.id, 2)"
                                        v-if="allowApproval"
                                    >
                                        <font-awesome-icon
                                            icon="fa-solid fa-thumbs-down"
                                            class="text-gray-700 px-2"
                                        />
                                    </a>
                                    <a
                                        class="cursor-pointer"
                                        @click="openEditModal(absence)"
                                        v-if="allowEdit"
                                    >
                                        <font-awesome-icon
                                            icon="fa-solid fa-pen"
                                            class="text-gray-700 px-2"
                                        />
                                    </a>
                                    <a
                                        class="cursor-pointer"
                                        @click="openDeleteModal(absence)"
                                        v-if="
                                            allowDelete &&
                                            allowActionsFor.includes(
                                                absence.subject_teacher_id
                                            )
                                        "
                                    >
                                        <font-awesome-icon
                                            icon="fa-solid fa-trash-can"
                                            class="text-gray-700 px-2"
                                        />
                                    </a>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </template>

        <template #footer>
            <SecondaryButton class="ml-3" @click="closeInfoModal">
                Zapri
            </SecondaryButton>
        </template>
    </DialogModal>

    <DialogModal :show="confirmingAbsenceDeletion" @close="closeDeleteModal">
        <template #title> Izbriši izostanek? </template>

        <template #content>
            <InputError :message="formDelete.errors.delete" class="mt-2" />
        </template>

        <template #footer>
            <SecondaryButton @click="closeDeleteModal">
                Prekliči
            </SecondaryButton>

            <DangerButton class="ml-3" @click="deleteAbsence">
                Izbriši izostanek
            </DangerButton>
        </template>
    </DialogModal>

    <DialogModal :show="absenceEditing" @close="closeEditModal">
        <template #title> Uredi opravičilo </template>

        <template #content>
            <form @submit.prevent="">
                <div class="px-4 py-5 bg-white sm:p-6">
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6 sm:col-span-4">
                            <InputLabel for="excuse" value="Opravičilo" />
                            <TextInput
                                id="excuse"
                                v-model="formEdit.excuse"
                                type="text"
                                class="mt-1 block w-full"
                            />
                            <InputError
                                :message="formEdit.errors.excuse"
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

            <PrimaryButton class="ml-3" @click="editAbsence">
                Shrani
            </PrimaryButton>
        </template>
    </DialogModal>
</template>
