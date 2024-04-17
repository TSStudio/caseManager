<template>
    <div id="content">
        <div class="topInfo">事务追踪</div>
        <div class="cnt-txt">
            <el-input
                v-model="inputCaseId"
                placeholder="请输入编号"
                @keydown.enter="handleClick"
            ></el-input>
            <div class="buttonContainer">
                <a @click="handleClick" class="action-button">查询</a>
            </div>
        </div>
        <div class="cnt-txt">当前事务编号：{{ currentCaseId }}</div>
        <div class="cnt-txt">状态：{{ prompt }}</div>
        <div class="cnt-txt" ref="trackerContainer" id="trackerContainer">
            <tracker v-if="ready" :caseId="currentCaseId"></tracker>
        </div>
    </div>
</template>
<script>
import tracker from "./tracker.vue";
import tapi from "./tapinterface.js";

export default {
    data() {
        return {
            inputCaseId: "",
            currentCaseId: "",
            ready: false,
            prompt: "请输入事务编号",
        };
    },
    components: {
        tracker,
    },
    methods: {
        setToCaseId(caseid) {
            this.inputCaseId = caseid;
            this.handleClick();
        },
        handleClick() {
            this.ready = false;
            this.currentCaseId = this.inputCaseId;
            if (this.inputCaseId == "") {
                this.prompt = "请输入事务编号";
                return;
            }
            let api = new tapi();
            api.check_existance(this.currentCaseId).then((res) => {
                if (res.data.exists == true) {
                    this.prompt = "查询成功";
                    this.ready = true;
                } else {
                    this.prompt = "事务不存在";
                }
            });
            this.prompt = "正在查询";
        },
    },
};
</script>
