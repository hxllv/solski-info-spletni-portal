<script setup>
import { useForm, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import Table from '@/Components/Table.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import Select from '@/Components/Select.vue';
import InputError from '@/Components/InputError.vue';
import DangerButton from '@/Components/DangerButton.vue';

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
const formAdd = useForm({
    teacher: '',
    custom_name: '',
});
const formDelete = useForm({
    teacher: '',
});
const formFilter = useForm({
    term: '',
    role: ''
});

formFilter.term = props.params.term
formFilter.role = props.params.role

// adding teachers

const closeAddingModal = () => {
    addingTeacher.value = false;
    formAdd.reset();
};

const openAddingModal = () => {
    addingTeacher.value = true;
};

const addUsers = () => {
    formAdd.post(route('subject.toggle', props.subject.id), {
        preserveScroll: true,
        onSuccess: () => closeAddingModal(),
        onError: () => {},
    });
};

// removing teachers

const closeRemoveModal = () => {
    removingTeacher.value = false;
    formDelete.reset();
};

const openRemoveModal = (id) => {
    removingTeacher.value = true;
    formDelete.teacher = id;
};

const removeUsers = () => {
    formDelete.post(route('subject.toggle', props.subject.id), {
        preserveScroll: true,
        onSuccess: () => closeRemoveModal(),
        onError: () => {},
    });
};

// modifying users

const hatedProps = ['created_at', 'current_team_id', 'email_verified_at', 'role_id', 'name', 'surname', 'two_factor_confirmed_at', 'updated_at'];

const usersMod = computed(() => {
    let newUsers = JSON.parse(JSON.stringify(props.users));

    newUsers.data.forEach(user => {
        user.fullname = `${user.name} ${user.surname}`
        user.role = user.role.name
        user.custom = user.pivot.custom_name ? user.pivot.custom_name : props.subject.name

        hatedProps.forEach(prop => {
            delete user[prop]
        })
    })

    return newUsers
});

console.log(props.potentialTeachers)
</script>

<template>
    <AdminLayout title="Dashboard" :backButtonURL="route('subjects')" :header="`Nosilci predmeta ${subject.name}`" v-slot="layout" :permission="permission">
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
                            <option v-for="role in roles" :value="role.id">{{role.name}}</option>
                        </Select>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-1 p-2 text-right flex items-end justify-end">
                        <Link preserve-scroll preserve-state :href="route('view.subject', subject.id)" :data="{ page: 1, term: '', role: '' }" class="mr-1" @click="formFilter.reset()">
                            <SecondaryButton>
                                Ponastavi
                            </SecondaryButton>
                        </Link>

                        <Link preserve-scroll preserve-state :href="route('view.subject', subject.id)" :data="{ page: 1, term: formFilter.term, role: formFilter.role }">
                            <PrimaryButton>
                                Uporabi
                            </PrimaryButton>
                        </Link>
                    </div>
                </div>

                <div class="md:grid md:grid-cols-3 md:gap-6" v-if="permission.includes('subjects.edit')">
                    <div class="mt-5 md:mt-0 md:col-span-1 p-2">
                        <PrimaryButton @click="openAddingModal">
                            Dodaj nosilca
                        </PrimaryButton>
                    </div>
                </div>

                <Table :data="usersMod" :headerNames="['Naziv', 'Ime', 'Email', 'Skupina pravic']" 
                    :sortedAs="['custom', 'fullname', 'email', 'role']" 
                    :allowEdit="false" :allowDelete="permission.includes('subjects.edit')" :allowMultiActions="false"
                    @delete="openRemoveModal"
                    :query="{ term: formFilter.term, role: formFilter.role }"
                    :buffer="layout.buffer"
                    routeName="view.subject"
                    :routeParams="[subject.id]"
                />

                <!-- deleting -->

                <DialogModal :show="removingTeacher" @close="closeRemoveModal"> 
                    <template #title>
                        Odstrani nosilca?
                    </template>

                    <template #content>
                        Vsi podatki vezani na tega nosilca bodo izbrisani!

                        <InputError :message="formDelete.errors.teacher" class="mt-2" />
                    </template>

                    <template #footer>
                        <SecondaryButton @click="closeRemoveModal">
                            Prekliči
                        </SecondaryButton>

                        <DangerButton
                            class="ml-3"
                            @click="removeUsers"
                        >
                            Izbriši nosilca
                        </DangerButton>
                    </template>
                </DialogModal>

                <!-- adding teachers -->

                <DialogModal :show="addingTeacher" @close="closeAddingModal"> 
                    <template #title>
                        Dodaj nosilca
                    </template>
                    <template #content>
                        <form @submit.prevent="">
                            <div
                                class="px-4 py-5 bg-white sm:p-6"
                            >
                                <div class="grid grid-cols-6 gap-6">
                                    <!-- Teacher -->
                                    <div class="col-span-6 sm:col-span-4">
                                        <InputLabel for="class" value="Nosilec" />
                                        <Select
                                            id="class"
                                            v-model="formAdd.teacher"
                                            class="mt-1 block w-full"
                                            autocomplete="class"
                                        >
                                            <option v-for="teacher in potentialTeachers" :value="teacher.id">{{`${teacher.name} ${teacher.surname} - ${teacher.email}`}}</option>
                                        </Select>
                                        <InputError :message="formAdd.errors.teacher" class="mt-2" />
                                    </div>
                                    <!-- Name -->
                                    <div class="col-span-6 sm:col-span-4">
                                        <InputLabel for="name" value="Naziv (neobvezno)" />
                                        <TextInput
                                            id="name"
                                            v-model="formAdd.custom_name"
                                            type="text"
                                            class="mt-1 block w-full"
                                            autocomplete="name"
                                        />
                                        <InputError :message="formAdd.errors.custom_name" class="mt-2" />
                                    </div>
                                </div>
                            </div>
                        </form>
                    </template>

                    <template #footer>
                        <SecondaryButton @click="closeAddingModal">
                            Prekliči
                        </SecondaryButton>

                        <PrimaryButton
                            class="ml-3"
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