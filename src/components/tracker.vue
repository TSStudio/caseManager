<template>
    <infoCard
        v-for="row in data.data"
        :row="row"
        :deleteable="deleteable"
    ></infoCard>
</template>
<script>
import tapi from "./tapinterface.js";
import infoCard from "./infoCard.vue";
export default {
    props: {
        caseId: {
            type: String,
            required: true,
        },
        deleteable: {
            type: Boolean,
            default: false,
        },
    },
    components: {
        infoCard,
    },
    data() {
        return {
            data: {},
        };
    },
    methods: {
        getInfo() {
            let api = new tapi();
            api.get_info(this.caseId).then((res) => {
                this.data = res.data;
            });
        },
    },
    mounted() {
        this.getInfo();
    },
};
</script>
