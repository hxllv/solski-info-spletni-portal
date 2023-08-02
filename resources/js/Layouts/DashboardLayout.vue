<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Select from "@/Components/Select.vue";
import InputLabel from "@/Components/InputLabel.vue";
import { Link, router } from "@inertiajs/vue3";
import { ref, watch } from "vue";

const props = defineProps({
    title: String,
    backButtonURL: String,
    permission: Array,
    users: Array,
    userId: String,
});

const userIdVal = ref(props.userId);

const buffer = ref(false);
buffer.value = false;

router.on("start", () => {
    buffer.value = true;
});

router.on("finish", () => {
    buffer.value = false;
});

watch(userIdVal, (newId) => {
    router.get(
        route().current(),
        {
            userId: newId,
        },
        {
            preserveScroll: true,
        }
    );
});
</script>

<template>
    <AppLayout :title="title" :permission="permission">
        <div
            class="py-5 m-2 max-w-7xl mt-0 sm:px-6 lg:px-8 mx-auto"
            v-if="users && users.length > 1"
        >
            <InputLabel for="class" value="Uporabnik" />
            <Select id="user" v-model="userIdVal" class="mt-1 block w-full">
                <option v-for="user in users" :value="user.id">
                    {{ user.full_name }}
                </option>
            </Select>
        </div>

        <div>
            <slot :buffer="buffer" />
        </div>
    </AppLayout>
</template>
