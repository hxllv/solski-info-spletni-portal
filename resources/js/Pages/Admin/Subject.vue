<script setup>
import { useForm, Link, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import Table from "@/Components/Table.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import DialogModal from "@/Components/DialogModal.vue";
import Select from "@/Components/Select.vue";
import InputError from "@/Components/InputError.vue";
import DangerButton from "@/Components/DangerButton.vue";

const props = defineProps({
    subject: Object,
    roles: Array,
    potentialTeachers: Object,
    users: Object,
    params: Object,
    permission: Array,
});

const addingTeacher = ref(false);
const removingTeacher = ref(false);
const selectedUsersAdding = ref(false);
const selectedUsers = ref(false);
const multiActionsAddingClass = ref("opacity-30 pointer-events-none");
const multiActionsClass = ref("opacity-30 pointer-events-none");

const formAdd = useForm({
    custom_name: "",
    detaching: false,
});
const formDelete = useForm({
    detaching: true,
});
const formFilter = useForm({
    term: "",
    role: "",
});

formFilter.term = props.params.term;
formFilter.role = props.params.role;

const formFilterAdding = useForm({
    term_adding: "",
    role_adding: "",
});

formFilterAdding.term_adding = props.params.term_adding;
formFilterAdding.role_adding = props.params.role_adding;

// adding teachers

const onAddingSelectChange = (val) => {
    selectedUsersAdding.value = val.length > 0 ? val : false;

    multiActionsAddingClass.value = "";
    if (!selectedUsersAdding.value)
        multiActionsAddingClass.value = "opacity-30 pointer-events-none";
};

const closeAddingModal = () => {
    addingTeacher.value = false;
    formAdd.reset();

    router.get(
        route("view.subject", props.subject.id),
        {
            page: props.params.page,
            term: props.params.term,
            role: props.params.role,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
};

const openAddingModal = () => {
    addingTeacher.value = true;
};

const addUsers = () => {
    formAdd
        .transform((data) => ({
            ...data,
            userIds: selectedUsersAdding.value,
        }))
        .post(route("subject.toggle", props.subject.id), {
            preserveScroll: true,
            onSuccess: () => closeAddingModal(),
            onError: () => {},
        });
};

// removing teachers

const onSelectChange = (val) => {
    selectedUsers.value = val.length > 0 ? val : false;

    multiActionsClass.value = "";
    if (!selectedUsers.value)
        multiActionsClass.value = "opacity-30 pointer-events-none";
};

const closeRemoveModal = () => {
    removingTeacher.value = false;
    formDelete.reset();
};

const openRemoveModal = () => {
    removingTeacher.value = true;
};

const removeUsers = () => {
    formDelete
        .transform((data) => ({
            ...data,
            userIds: selectedUsers.value,
        }))
        .post(route("subject.toggle", props.subject.id), {
            preserveScroll: true,
            onSuccess: () => closeRemoveModal(),
            onError: () => {},
        });
};

// modifying users

const usersMod = computed(() => {
    let newUsers = JSON.parse(JSON.stringify(props.users));

    newUsers.data.forEach((user) => {
        user.custom = user.pivot.name;
    });

    return newUsers;
});
</script>

<template>
    <AdminLayout
        :title="`Nosilci predmeta ${subject.name}`"
        :backButtonURL="route('subjects')"
        v-slot="layout"
        :permission="permission"
    >
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div>
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="mt-5 md:mt-0 md:col-span-1 p-2">
                        <InputLabel for="term" value="Iskalni niz" />
                        <TextInput
                            id="term"
                            v-model="formFilter.term"
                            type="text"
                            class="mt-1 block w-full"
                            autocomplete="term"
                        />
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-1 p-2">
                        <InputLabel for="roleFilter" value="Skupina pravic" />
                        <Select
                            id="roleFilter"
                            v-model="formFilter.role"
                            class="mt-1 block w-full"
                            autocomplete="role"
                            :defaultValue="''"
                        >
                            <option value="">Vsi</option>
                            <option v-for="role in roles" :value="role.id">
                                {{ role.name }}
                            </option>
                        </Select>
                    </div>
                    <div
                        class="mt-5 md:mt-0 md:col-span-1 p-2 text-right flex items-end justify-end"
                    >
                        <Link
                            preserve-scroll
                            preserve-state
                            :href="route('view.subject', subject.id)"
                            :data="{ page: 1, term: '', role: '' }"
                            class="mr-1"
                            @click="formFilter.reset()"
                        >
                            <SecondaryButton> Ponastavi </SecondaryButton>
                        </Link>

                        <Link
                            preserve-scroll
                            preserve-state
                            :href="route('view.subject', subject.id)"
                            :data="{
                                page: 1,
                                term: formFilter.term,
                                role: formFilter.role,
                            }"
                        >
                            <PrimaryButton> Uporabi </PrimaryButton>
                        </Link>
                    </div>
                </div>

                <div
                    class="md:grid md:grid-cols-3 md:gap-6"
                    v-if="permission.includes('subjects.edit')"
                >
                    <div class="mt-5 md:mt-0 md:col-span-1 p-2">
                        <SecondaryButton
                            class="mr-1"
                            :class="multiActionsClass"
                            @click="openRemoveModal"
                        >
                            Odstrani nosilce
                        </SecondaryButton>
                        <PrimaryButton @click="openAddingModal">
                            Dodaj nosilca
                        </PrimaryButton>
                    </div>
                </div>

                <Table
                    :data="usersMod"
                    :headerNames="['Naziv', 'Ime', 'Email', 'Skupina pravic']"
                    :sortedAs="['custom', 'full_name', 'email', 'role_name']"
                    :allowEdit="false"
                    :allowDelete="false"
                    :allowMultiActions="permission.includes('subjects.edit')"
                    :query="{ term: formFilter.term, role: formFilter.role }"
                    :buffer="layout.buffer"
                    @selectedChange="onSelectChange"
                    routeName="view.subject"
                    :routeParams="[subject.id]"
                />

                <!-- deleting -->

                <DialogModal :show="removingTeacher" @close="closeRemoveModal">
                    <template #title> Odstrani nosilce? </template>

                    <template #content>
                        <p>Vsi podatki vezani na te nosilce bodo izbrisani!</p>
                        <p class="py-2">
                            (Ocene v redovalnici, izostanki, datumi ocenjevanja,
                            vnosi v urnik, nadomeščanja)
                        </p>

                        <InputError
                            :message="formDelete.errors.teacher"
                            class="mt-2"
                        />
                    </template>

                    <template #footer>
                        <SecondaryButton @click="closeRemoveModal">
                            Prekliči
                        </SecondaryButton>

                        <DangerButton class="ml-3" @click="removeUsers">
                            Izbriši nosilca
                        </DangerButton>
                    </template>
                </DialogModal>

                <!-- adding teachers -->

                <DialogModal
                    :show="addingTeacher"
                    @close="closeAddingModal"
                    maxWidth="4xl"
                >
                    <template #title> Dodaj nosilca </template>
                    <template #content>
                        <form @submit.prevent="">
                            <div class="px-4 py-5 bg-white sm:p-6">
                                <div class="grid grid-cols-6 gap-6">
                                    <!-- Name -->
                                    <div class="col-span-6 sm:col-span-4">
                                        <InputLabel
                                            for="name"
                                            value="Naziv (neobvezno)"
                                        />
                                        <TextInput
                                            id="name"
                                            v-model="formAdd.custom_name"
                                            type="text"
                                            class="mt-1 block w-full"
                                            autocomplete="name"
                                        />
                                        <InputError
                                            :message="
                                                formAdd.errors.custom_name
                                            "
                                            class="mt-2"
                                        />
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="md:grid md:grid-cols-3 md:gap-6">
                            <div class="mt-5 md:mt-0 md:col-span-1 p-2">
                                <InputLabel for="term" value="Iskalni niz" />
                                <TextInput
                                    id="term"
                                    v-model="formFilterAdding.term_adding"
                                    type="text"
                                    class="mt-1 block w-full"
                                    autocomplete="term"
                                />
                            </div>
                            <div class="mt-5 md:mt-0 md:col-span-1 p-2">
                                <InputLabel
                                    for="roleFilter"
                                    value="Skupina pravic"
                                />
                                <Select
                                    id="roleFilter"
                                    v-model="formFilterAdding.role_adding"
                                    class="mt-1 block w-full"
                                    autocomplete="role"
                                    :defaultValue="''"
                                >
                                    <option value="">Vsi</option>
                                    <option
                                        v-for="role in roles"
                                        :value="role.id"
                                    >
                                        {{ role.name }}
                                    </option>
                                </Select>
                            </div>
                            <div
                                class="mt-5 md:mt-0 md:col-span-1 p-2 text-right flex items-end justify-end"
                            >
                                <Link
                                    preserve-scroll
                                    preserve-state
                                    :href="route('view.subject', subject.id)"
                                    :data="{
                                        page: props.params.page,
                                        ps_page: 1,
                                        term_adding: '',
                                        role_adding: '',
                                    }"
                                    class="mr-1"
                                    @click="formFilterAdding.reset()"
                                >
                                    <SecondaryButton>
                                        Ponastavi
                                    </SecondaryButton>
                                </Link>

                                <Link
                                    preserve-scroll
                                    preserve-state
                                    :href="route('view.subject', subject.id)"
                                    :data="{
                                        page: props.params.page,
                                        ps_page: 1,
                                        term_adding:
                                            formFilterAdding.term_adding,
                                        role_adding:
                                            formFilterAdding.role_adding,
                                    }"
                                >
                                    <PrimaryButton> Uporabi </PrimaryButton>
                                </Link>
                            </div>
                        </div>
                        <Table
                            :data="potentialTeachers"
                            :headerNames="['Ime', 'Email', 'Skupina pravic']"
                            :sortedAs="['full_name', 'email', 'role_name']"
                            :allowEdit="false"
                            :allowDelete="false"
                            :allowMultiActions="true"
                            :query="{
                                page: props.params.page,
                                term: formFilter.term,
                                role: formFilter.role,
                                term_adding: formFilterAdding.term_adding,
                                role_adding: formFilterAdding.role_adding,
                            }"
                            :buffer="layout.buffer"
                            @selectedChange="onAddingSelectChange"
                            pageName="ps_page"
                            routeName="view.subject"
                            :routeParams="[subject.id]"
                        />
                    </template>

                    <template #footer>
                        <SecondaryButton @click="closeAddingModal">
                            Prekliči
                        </SecondaryButton>

                        <PrimaryButton
                            class="ml-3"
                            :class="multiActionsAddingClass"
                            @click="addUsers"
                        >
                            Shrani
                        </PrimaryButton>
                    </template>
                </DialogModal>
            </div>
        </div>
    </AdminLayout>
</template>
