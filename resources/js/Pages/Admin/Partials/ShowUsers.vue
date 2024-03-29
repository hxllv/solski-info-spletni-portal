<script setup>
import { ref, computed } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import DangerButton from '@/Components/DangerButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Table from '@/Components/Table.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Select from '@/Components/Select.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

const props = defineProps({
    users: Object,
    roles: Array,
    classes: Array,
    params: Object,
    buffer: Boolean,
    permission: Array
});

const confirmingUserDeletion = ref(false);
const userEditing = ref(false);
const editingAccountOwner = ref(false);
const userToDelete = ref(false);
const userToEdit = ref(false);
const selectedUsers = ref(false);
const multiActionsClass = ref('opacity-30 pointer-events-none');
const showMultiModal = ref(false)
const formDelete = useForm({});
const formEdit = useForm({
    name: '',
    surname: '',
    email: '',
    role: '',
    class: '',
});
const formFilter = useForm({
    term: '',
    role: '',
    reg: ''
});

formFilter.term = props.params.term
formFilter.role = props.params.role

const formMulti = useForm({
    class: '',
    role: '',
});

// deleting

const closeDeleteModal = () => {
    confirmingUserDeletion.value = false;
    userToDelete.value = null
};

const openDeleteModal = (id) => {
    confirmingUserDeletion.value = true;
    userToDelete.value = id
};

const deleteUser = () => {
    formDelete.delete(route('user.delete', userToDelete.value), {
        preserveScroll: true,
        onSuccess: () => closeDeleteModal(),
        onError: () => {},
    });
};

// editing

const closeEditModal = () => {
    userEditing.value = false;
    userToEdit.value = null

    formEdit.reset()
};

const openEditModal = (id) => {
    userEditing.value = true;
    userToEdit.value = id;

    const userTemp = props.users.data.filter(user => { return user.id === id })[0]

    editingAccountOwner.value = userTemp.is_account_owner
    formEdit.name = userTemp.name
    formEdit.surname = userTemp.surname
    formEdit.email = userTemp.email
    formEdit.role = String(userTemp.role_id)
    formEdit.class = String(userTemp.school_class_id ? userTemp.school_class_id : -1)
};

const editUserData = () => {
    formEdit.put(route('user.update', userToEdit.value), {
        errorBag: 'updateProfileInformation',
        preserveScroll: true,
        onSuccess: () => closeEditModal(),
    });
}

// multi actions

const onSelectChange = (val) => {
    selectedUsers.value = val.length > 0 ? val : false;

    multiActionsClass.value = ''
    if (!selectedUsers.value)
        multiActionsClass.value = 'opacity-30 pointer-events-none'
}

const closeMultiModal = () => {
    showMultiModal.value = false;

    formMulti.reset()
};

const openMultiModal = (modalName) => {
    showMultiModal.value = modalName;
};

const multiActionDo = (modalName) => {
    if (modalName === 'role' || modalName === 'class') {
        formMulti.transform(data => ({
            ...data,
            userIds: selectedUsers.value
        })).put(route('multi.user.update'), {
            preserveScroll: true,
            onSuccess: () => closeMultiModal(),
        });
    }
    else if (modalName === 'delete') {
        formMulti.transform(() => ({
            userIds: selectedUsers.value
        })).delete(route('multi.user.delete'), {
            preserveScroll: true,
            onSuccess: () => closeMultiModal(),
        });
    }
};
</script>

<template>
    <!-- filtering -->
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
            >
                <option value="">Vsi</option>
                <option v-for="role in roles" :value="role.id">{{role.name}}</option>
            </Select>
        </div>
        <div class="mt-5 md:mt-0 md:col-span-1 p-2">
            <InputLabel for="regFilter" value="Status registracije" />
            <Select
                id="regFilter"
                v-model="formFilter.reg"
                class="mt-1 block w-full"
            >
                <option value="">Vsi</option>
                <option value="reg">Registrirani</option>
                <option value="neg">Neregistrirani</option>
            </Select>
        </div>
        <div class="mt-5 md:mt-0 md:col-start-3 md:row-start-2 p-2 text-right flex items-end justify-end">
            <Link preserve-scroll preserve-state :href="route('users')" :data="{ page: 1, term: '', role: '', reg: '' }" class="mr-1" @click="formFilter.reset()">
                <SecondaryButton>
                    Ponastavi
                </SecondaryButton>
            </Link>

            <Link preserve-scroll preserve-state :href="route('users')" :data="{ page: 1, term: formFilter.term, role: formFilter.role, reg: formFilter.reg }">
                <PrimaryButton>
                    Uporabi
                </PrimaryButton>
            </Link>
        </div>
    </div>

    <!-- multi actions -->
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="mt-5 md:mt-0 md:col-span-1 p-2">
            <Dropdown align="left" :class="multiActionsClass">
                <template #trigger>
                    <SecondaryButton>
                        Akcije
                    </SecondaryButton>
                </template>
                <template #content>
                    <DropdownLink as="button" @click="openMultiModal('role')">
                        Dodeli skupino pravic
                    </DropdownLink>
                    <DropdownLink as="button" @click="openMultiModal('class')">
                        Dodeli razred
                    </DropdownLink>
                    <DropdownLink as="button" @click="openMultiModal('delete')">
                        Izbriši
                    </DropdownLink>
                </template>
            </Dropdown>
        </div>
    </div>
    
    <Table :data="users" :headerNames="['Ime', 'Email', 'Razred', 'Skupina pravic']" 
        :sortedAs="['full_name', 'email', 'class_name', 'role_name']" 
        :allowEdit="permission.includes('users.edit')" :allowDelete="permission.includes('users.delete')" 
        :allowMultiActions="true"
        @edit="openEditModal" 
        @delete="openDeleteModal"
        detailsURL="view.user"
        :query="{ term: formFilter.term, role: formFilter.role }"
        :buffer="buffer"
        @selectedChange="onSelectChange"
        routeName="users"
    />

    <!-- deleting -->

    <DialogModal :show="confirmingUserDeletion" @close="closeDeleteModal"> 
        <template #title>
            Izbriši uporabnika?
        </template>

        <template #content>
            Vsi podatki vezani na tega uporabnika bodo izbrisani!

            <InputError :message="formDelete.errors.delete" class="mt-2" />
        </template>

        <template #footer>
            <SecondaryButton @click="closeDeleteModal">
                Prekliči
            </SecondaryButton>

            <DangerButton
                class="ml-3"
                @click="deleteUser"
            >
                Izbriši uporabnika
            </DangerButton>
        </template>
    </DialogModal>

    <!-- editing -->

    <DialogModal :show="userEditing" @close="closeEditModal"> 
        <template #title>
            Uredi uporabnika
        </template>

        <template #content>
            <form @submit.prevent="">
                <div
                    class="px-4 py-5 bg-white sm:p-6"
                >
                    <!-- Name -->
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6 sm:col-span-4">
                            <InputLabel for="name" value="Ime" />
                            <TextInput
                                id="name"
                                v-model="formEdit.name"
                                type="text"
                                class="mt-1 block w-full"
                                autocomplete="name"
                            />
                            <InputError :message="formEdit.errors.name" class="mt-2" />
                        </div>

                        <!-- Surname -->
                        <div class="col-span-6 sm:col-span-4">
                            <InputLabel for="surname" value="Priimek" />
                            <TextInput
                                id="surname"
                                v-model="formEdit.surname"
                                type="text"
                                class="mt-1 block w-full"
                                autocomplete="surname"
                            />
                            <InputError :message="formEdit.errors.surname" class="mt-2" />
                        </div>

                        <!-- Email -->
                        <div class="col-span-6 sm:col-span-4">
                            <InputLabel for="email" value="Email" />
                            <TextInput
                                id="email"
                                v-model="formEdit.email"
                                type="text"
                                class="mt-1 block w-full"
                                autocomplete="email"
                            />
                            <InputError :message="formEdit.errors.email" class="mt-2" />
                        </div>

                        <!-- Role -->
                        <div class="col-span-6 sm:col-span-4" v-if="!editingAccountOwner">
                            <InputLabel for="role" value="Skupina pravic" />
                            <Select
                                id="role"
                                v-model="formEdit.role"
                                class="mt-1 block w-full"
                                autocomplete="role"
                            >
                                <option v-for="role in roles" :value="role.id">{{role.name}}</option>
                            </Select>
                            <InputError :message="formEdit.errors.role" class="mt-2" />
                        </div>

                        <!-- Class -->
                        <div class="col-span-6 sm:col-span-4">
                            <InputLabel for="class" value="Razred" />
                            <Select
                                id="class"
                                v-model="formEdit.class"
                                class="mt-1 block w-full"
                                autocomplete="class"
                            >
                                <option value="-1">/</option>
                                <option v-for="sClass in classes" :value="sClass.id">{{sClass.name}}</option>
                            </Select>
                            <InputError :message="formEdit.errors.class" class="mt-2" />
                        </div>
                    </div>
                </div>
            </form>
        </template>

        <template #footer>
            <SecondaryButton @click="closeEditModal">
                Prekliči
            </SecondaryButton>

            <PrimaryButton
                class="ml-3"
                @click="editUserData"
            >
                Shrani
            </PrimaryButton>
        </template>
    </DialogModal>

    <!-- multiactions -->

    <DialogModal :show="typeof showMultiModal == 'string'" @close="closeMultiModal"> 
        <template #title v-if="showMultiModal === 'role'">
            Dodeli skupino pravic
        </template>
        <template #title v-if="showMultiModal === 'class'">
            Dodeli razred
        </template>
        <template #title v-if="showMultiModal === 'delete'">
            Izbriši uporabnike?
        </template>

        <template #content v-if="showMultiModal === 'role'">
            <form @submit.prevent="">
                <div class="px-4 py-5 bg-white sm:p-6">
                    <div class="grid grid-cols-6 gap-6">
                        <!-- Role -->
                        <div class="col-span-6 sm:col-span-4">
                            <InputLabel for="role" value="Skupina pravic" />
                            <Select
                                id="role"
                                v-model="formMulti.role"
                                class="mt-1 block w-full"
                                autocomplete="role"
                            >
                                <option v-for="role in roles" :value="role.id">{{role.name}}</option>
                            </Select>
                            <InputError :message="formMulti.errors.role" class="mt-2" />
                        </div>
                    </div>
                </div>
            </form>
        </template>
        <template #content v-if="showMultiModal === 'class'">
            <form @submit.prevent="">
                <div class="px-4 py-5 bg-white sm:p-6">
                    <div class="grid grid-cols-6 gap-6">
                        <!-- Class -->
                        <div class="col-span-6 sm:col-span-4">
                            <InputLabel for="class" value="Razred" />
                            <Select
                                id="class"
                                v-model="formMulti.class"
                                class="mt-1 block w-full"
                                autocomplete="role"
                            >
                                <option value="-1">/</option>
                                <option v-for="sClass in classes" :value="sClass.id">{{sClass.name}}</option>
                            </Select>
                            <InputError :message="formMulti.errors.class" class="mt-2" />
                        </div>
                    </div>
                </div>
            </form>
        </template>
        <template #content v-if="showMultiModal === 'delete'">
            Vsi podatki vezani na te uporabnike bodo izbrisani!

            <InputError :message="formMulti.errors.delete" class="mt-2" />
        </template>

        <template #footer v-if="showMultiModal === 'role' || showMultiModal === 'class'">
            <SecondaryButton @click="closeMultiModal">
                Prekliči
            </SecondaryButton>

            <PrimaryButton
                class="ml-3"
                @click="multiActionDo(showMultiModal)"
            >
                Shrani
            </PrimaryButton>
        </template>
        <template #footer v-if="showMultiModal === 'delete'">
            <SecondaryButton @click="closeMultiModal">
                Prekliči
            </SecondaryButton>

            <DangerButton
                class="ml-3"
                @click="multiActionDo(showMultiModal)"
            >
                Izbriši uporabnike
            </DangerButton>
        </template>
    </DialogModal>
</template>