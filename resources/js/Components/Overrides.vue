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
    overrides: Object,
    entries: Object,
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

const overrideInfo = ref(false);
const selectedOverride = ref(false);

const confirmingOverrideDeletion = ref(false);
const overrideEditing = ref(false);

const formDelete = useForm({});
const formEdit = useForm({
    date: "",
    note: "",
});

// info

const closeInfoModal = () => {
    overrideInfo.value = false;
    selectedOverride.value = false;
};

const openInfoModal = (override, entry) => {
    overrideInfo.value = true;
    selectedOverride.value = {
        override: override,
        entry: entry,
    };
};

// deleting

const closeDeleteModal = (fullClose = false) => {
    overrideInfo.value = true;
    confirmingOverrideDeletion.value = false;

    if (fullClose) closeInfoModal();
};

const openDeleteModal = () => {
    overrideInfo.value = false;
    confirmingOverrideDeletion.value = true;
};

const deleteOverride = () => {
    formDelete.delete(
        route("overrides.delete", selectedOverride.value.override.id),
        {
            preserveScroll: true,
            onSuccess: () => closeDeleteModal(true),
            onError: () => {},
        }
    );
};

// editing

const closeEditModal = (fullClose = false) => {
    overrideInfo.value = true;
    overrideEditing.value = false;

    formEdit.reset();

    if (fullClose) closeInfoModal();
};

const openEditModal = () => {
    overrideInfo.value = false;
    overrideEditing.value = true;

    formEdit.note = selectedOverride.value.override.note;
};

const editOverride = () => {
    formEdit.put(
        route("overrides.update", selectedOverride.value.override.id),
        {
            preserveScroll: true,
            onSuccess: () => closeEditModal(true),
        }
    );
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
                        <th class="px-6 py-3 border-r col-span-3">
                            Predmet
                            <font-awesome-icon
                                class="px-2"
                                icon="fa-solid fa-arrow-right"
                            />
                            nadomestni predmet
                        </th>
                    </tr>
                </thead>
                <tbody class="text-xs md:text-sm lg:text-base">
                    <tr
                        v-for="(override, key) in overrides"
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
                            <div v-for="overridey in override">
                                <span
                                    @click="
                                        openInfoModal(
                                            overridey,
                                            entries[overridey.hour].filter(
                                                (entry) => {
                                                    return (
                                                        entry.day ==
                                                        overridey.day
                                                    );
                                                }
                                            )[0]
                                        )
                                    "
                                    class="cursor-pointer"
                                >
                                    <span>
                                        {{
                                            entries[overridey.hour].filter(
                                                (entry) => {
                                                    return (
                                                        entry.day ==
                                                        overridey.day
                                                    );
                                                }
                                            )[0].subject_teacher.name
                                        }}
                                        -
                                        {{
                                            entries[overridey.hour].filter(
                                                (entry) => {
                                                    return (
                                                        entry.day ==
                                                        overridey.day
                                                    );
                                                }
                                            )[0].subject_teacher.users_full_name
                                        }}
                                    </span>
                                    <font-awesome-icon
                                        class="px-2"
                                        icon="fa-solid fa-arrow-right"
                                    />
                                    <span v-if="overridey.subject_teacher">
                                        {{ overridey.subject_teacher.name }} -
                                        {{
                                            overridey.subject_teacher
                                                .users_full_name
                                        }}
                                    </span>
                                    <span v-else> / </span>
                                </span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <DialogModal :show="overrideInfo" @close="closeInfoModal">
        <template #content>
            <div class="py-4 font-medium">Predmet</div>
            <ul class="list-disc py-2 pl-4">
                <li>
                    Predmet: {{ selectedOverride.entry.subject_teacher.name }}
                </li>
                <li>
                    Nosilec:
                    {{ selectedOverride.entry.subject_teacher.users_full_name }}
                </li>
                <li>
                    Ura:
                    {{ selectedOverride.entry.hour_name }}
                </li>
            </ul>
            <div class="py-4 font-medium">Nadomestni predmet</div>
            <ul class="list-disc py-2 pl-4">
                <li>
                    Predmet:
                    {{ selectedOverride.override.subject_teacher.name }}
                </li>
                <li>
                    Nosilec:
                    {{
                        selectedOverride.override.subject_teacher
                            .users_full_name
                    }}
                </li>
                <li v-if="selectedOverride.override.note">
                    Opombe: {{ selectedOverride.override.note }}
                </li>
            </ul>
        </template>

        <template #footer>
            <DangerButton
                v-if="
                    allowDelete &&
                    allowActionsFor.includes(
                        selectedOverride.override.subject_teacher_id
                    )
                "
                @click="openDeleteModal"
            >
                Izbriši
            </DangerButton>
            <PrimaryButton
                v-if="
                    allowEdit &&
                    allowActionsFor.includes(
                        selectedOverride.override.subject_teacher_id
                    )
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

    <DialogModal :show="confirmingOverrideDeletion" @close="closeDeleteModal">
        <template #title> Izbriši nadomeščanje? </template>

        <template #content>
            <InputError :message="formDelete.errors.delete" class="mt-2" />
        </template>

        <template #footer>
            <SecondaryButton @click="closeDeleteModal">
                Prekliči
            </SecondaryButton>

            <DangerButton class="ml-3" @click="deleteOverride">
                Izbriši nadomeščanje
            </DangerButton>
        </template>
    </DialogModal>

    <DialogModal :show="overrideEditing" @close="closeEditModal">
        <template #title> Uredi nadomeščanje </template>

        <template #content>
            <form @submit.prevent="">
                <div class="px-4 py-5 bg-white sm:p-6">
                    <div class="grid grid-cols-6 gap-6">
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

            <PrimaryButton class="ml-3" @click="editOverride">
                Shrani
            </PrimaryButton>
        </template>
    </DialogModal>
</template>
