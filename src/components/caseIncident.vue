<template>
    <div id="content">
        <el-result
            icon="info"
            title="提示"
            :subTitle="noPermissionReason"
            v-if="!hasPermission"
        >
            <template v-slot:extra>
                <el-button
                    type="primary"
                    size="medium"
                    @click="check_permission()"
                    :disabled="isCheckingPermission"
                    >重试</el-button
                >
            </template>
        </el-result>
        <div v-if="hasPermission">
            <div class="topInfo">事务管理</div>
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
            <div
                class="cnt-txt"
                ref="trackerContainer"
                id="trackerContainer"
                v-if="ready"
                v-loading="loading"
            >
                <el-input
                    v-model="inputNewIncident"
                    type="textarea"
                    placeholder="请输入新事件"
                    @keydown.enter="handleClickNew"
                ></el-input>
                <div class="buttonContainer">
                    <a @click="handleClickNew" class="action-button">创建</a>
                </div>
                <tracker
                    :caseId="currentCaseId"
                    ref="tracker"
                    deleteable="true"
                ></tracker>
            </div>
        </div>
    </div>
</template>
<script>
import tracker from "./tracker.vue";
import tapi from "./tapinterface.js";

export default {
    data() {
        return {
            hasPermission: false,
            noPermissionReason: "正在检查您的权限，请稍等",
            isCheckingPermission: true,
            inputCaseId: "",
            currentCaseId: "",
            ready: false,
            prompt: "请输入事务编号",
            inputNewIncident: "",
            loading: false,
        };
    },
    components: {
        tracker,
    },
    methods: {
        handleClickNew() {
            let api = new tapi();
            this.loading = true;
            api.new_incident(this.currentCaseId, this.inputNewIncident)
                .then((res) => {
                    if (res.status == "success") {
                        ElNotification({
                            title: "创建成功",
                            message: "新事件已创建",
                            type: "success",
                        });
                    } else {
                        ElNotification({
                            title: "创建失败",
                            message: "请检查您的网络连接",
                            type: "error",
                        });
                    }
                    this.$refs.tracker.data = res.data;
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        check_permission(override = false) {
            if (this.isCheckingPermission && !override) return;
            this.isCheckingPermission = true;
            let api = new tapi();
            api.checkPermission("case_management")
                .then((result) => {
                    if (result.permission) {
                        this.hasPermission = true;
                    } else {
                        this.noPermissionReason = result.noPermissionReason;
                    }
                })
                .catch((error) => {
                    console.log(error);
                })
                .finally(() => {
                    this.isCheckingPermission = false;
                });
        },
        handleClick() {
            this.ready = false;
            this.currentCaseId = this.inputCaseId;
            if (this.inputCaseId == "") {
                this.prompt = "请输入事务编号";
                return;
            }
            this.loading = true;
            let api = new tapi();
            api.check_existance(this.currentCaseId)
                .then((res) => {
                    if (res.data.exists == true) {
                        this.prompt = "查询成功";
                        this.ready = true;
                    } else {
                        this.prompt = "事务不存在";
                    }
                })
                .finally(() => {
                    this.loading = false;
                });
            this.prompt = "正在查询";
        },
    },
    mounted() {
        this.check_permission(true);
    },
};
</script>
