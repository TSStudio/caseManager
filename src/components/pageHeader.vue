<template>
    <div id="navigation">
        <div id="function-selector">
            <div
                :class="{
                    page: true,
                    'page-selected': currentFunction == 'newCase',
                }"
                @click="handleClick('newCase')"
            >
                新事务
            </div>
            <div
                :class="{
                    page: true,
                    'page-selected': currentFunction == 'caseTrack',
                }"
                @click="handleClick('caseTrack')"
            >
                事务追踪
            </div>
            <div
                :class="{
                    page: true,
                    'page-selected': currentFunction == 'caseIncident',
                }"
                @click="handleClick('caseIncident')"
            >
                事务管理
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        currentFunction: {
            type: String,
            default: "newCase",
            required: false,
        },
    },
    methods: {
        handleClick(tab) {
            this.$parent.switchCurrentFunction(tab);
        },
        handleToggle() {
            this.$parent.switchDarkMode();
        },
    },
};
</script>
