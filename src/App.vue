<template>
    <pageHeader v-model:currentFunction="currentFunction"></pageHeader>
    <newCase v-show="currentFunction == 'newCase'"></newCase>
    <caseTrack v-show="currentFunction == 'caseTrack'" ref="track"></caseTrack>
    <caseIncident v-show="currentFunction == 'caseIncident'"></caseIncident>
</template>

<script>
import pageHeader from "./components/pageHeader.vue";
import newCase from "./components/newCase.vue";
import caseTrack from "./components/caseTrack.vue";
import caseIncident from "./components/caseIncident.vue";

export default {
    data() {
        return {
            currentFunction: "newCase",
        };
    },
    components: {
        pageHeader,
        newCase,
        caseTrack,
        caseIncident,
    },
    methods: {
        switchCurrentFunction(fun) {
            this.currentFunction = fun;
        },
        getQueryVariable(variable) {
            var query = window.location.search.substring(1);
            var vars = query.split("&");
            for (var i = 0; i < vars.length; i++) {
                var pair = vars[i].split("=");
                if (pair[0] == variable) {
                    return pair[1];
                }
            }
            return false;
        },
    },
    mounted() {
        let id = this.getQueryVariable("caseid");
        if (id) {
            this.$refs.track.setToCaseId(id);
            this.currentFunction = "caseTrack";
        }
    },
};
</script>
