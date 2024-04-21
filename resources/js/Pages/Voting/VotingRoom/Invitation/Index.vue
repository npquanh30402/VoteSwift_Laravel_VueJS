<template>
    <div v-show="roomSettings">
        <div class="d-flex flex-column gap-3">
            <div
                class="hstack gap-3 align-items-center justify-content-between"
            >
                <div class="form-check form-switch">
                    <input
                        id="SpecificVoterSwitch"
                        v-model="onlyInvitation"
                        :class="{ 'un-interactive': isPublish }"
                        class="form-check-input"
                        role="switch"
                        type="checkbox"
                        @change="toggleInvitation"
                    />
                    <label class="form-check-label" for="musicPlayerSwitch"
                        >Only Voters I invite Can Join and Vote</label
                    >
                </div>
                <div
                    :class="[onlyInvitation ? '' : 'un-interactive']"
                    class="form-check form-switch"
                >
                    <input
                        id="waitVoterSwitch"
                        v-model="waitForVoters"
                        :class="{ 'un-interactive': isPublish }"
                        class="form-check-input"
                        role="switch"
                        type="checkbox"
                        @change="toggleWaitForVoters"
                    />
                    <label class="form-check-label" for="waitVoterSwitch"
                        >Wait Until Voters Joined to Start Voting</label
                    >
                </div>
            </div>
            <div
                :class="[onlyInvitation ? '' : 'un-interactive']"
                class="row g-3"
            >
                <InvitationSidebar
                    :tabs="tabData"
                    @switch-tab="handleSwitchTab"
                />
                <div class="mt-3">
                    <transition mode="out-in" name="fade">
                        <KeepAlive>
                            <component
                                :is="tabData[currentTab].component"
                                :room="room"
                            ></component>
                        </KeepAlive>
                    </transition>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { usePage } from "@inertiajs/vue3";
import { computed, onMounted, ref, watch } from "vue";
import { useVotingSettingStore } from "@/Stores/voting-settings.js";
import InvitationVoter from "@/Pages/Voting/VotingRoom/Invitation/InvitationVoter.vue";
import InvitationMail from "@/Pages/Voting/VotingRoom/Invitation/InvitationMail.vue";
import InvitationSidebar from "@/Pages/Voting/VotingRoom/Invitation/InvitationSidebar.vue";

const props = defineProps(["room"]);
const votingSettingStore = useVotingSettingStore();

const authUser = computed(() => usePage().props.authUser.user);
const roomSettings = computed(() => votingSettingStore.settings[props.room.id]);

const onlyInvitation = ref(false);
const waitForVoters = ref(false);
const isPublish = computed(() => props.room.is_published === 1);
const tabData = {
    InvitationVoter: {
        component: InvitationVoter,
        name: "Voters",
        componentName: "InvitationVoter",
    },
    InvitationMail: {
        component: InvitationMail,
        name: "Mail",
        componentName: "InvitationMail",
    },
};

const currentTab = ref(tabData.InvitationVoter.componentName);

watch(
    () => roomSettings.value,
    () => {
        onlyInvitation.value = roomSettings.value.invitation_only === 1;
        waitForVoters.value = roomSettings.value.wait_for_voters === 1;
    },
);

onMounted(() => {
    votingSettingStore.fetchSettings(props.room.id);

    if (roomSettings.value) {
        onlyInvitation.value = roomSettings.value.invitation_only === 1;
        waitForVoters.value = roomSettings.value.wait_for_voters === 1;
    }
});

const updateSetting = (key, value) => {
    const formData = new FormData();
    formData.append(key, value);

    votingSettingStore.updateSettings(props.room.id, formData);
};

const toggleInvitation = () => {
    updateSetting("invitation_only", onlyInvitation.value);

    if (waitForVoters.value === true) {
        waitForVoters.value = false;
        toggleWaitForVoters();
    }
};

const toggleWaitForVoters = () => {
    updateSetting("wait_for_voters", waitForVoters.value);
};

const handleSwitchTab = (tabName) => {
    currentTab.value = tabName;
};
</script>
