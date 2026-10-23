<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { PhSpinnerGap } from '@phosphor-icons/vue';
import { reviewerTopicCopy } from '@/i18n/reviewerTopics';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import type { AcademicTopicRouting } from '@/types/academic';

const props = defineProps<{
    submissionId: string;
    routing: AcademicTopicRouting;
}>();
const { locale } = useI18n();
const c = computed(() => reviewerTopicCopy(locale.value));
const form = useForm({
    track_id: props.routing.currentTrackId ?? '',
    reason: '',
});
const routingError = computed(
    () => (form.errors as Record<string, string | undefined>).routing,
);
function name(
    topic: { code: string; name_i18n: Record<string, string> } | null,
) {
    return topic
        ? `${topic.code} · ${topic.name_i18n[locale.value] ?? topic.name_i18n.en ?? ''}`
        : c.value.noTopic;
}
function save() {
    if (form.processing || !props.routing.canCorrect) return;
    form.post(`/academic/submissions/${props.submissionId}/topic`, {
        preserveScroll: true,
        onSuccess: () => form.reset('reason'),
    });
}
</script>

<template>
    <Card class="w-full min-w-0 [overflow-wrap:anywhere]">
        <CardHeader
            ><CardTitle>{{ c.currentRouting }}</CardTitle>
            <p class="text-sm text-muted-foreground">
                {{ c.routingHelp }}
            </p></CardHeader
        >
        <CardContent class="grid min-w-0 gap-5">
            <dl class="grid min-w-0 gap-4 md:grid-cols-2">
                <div class="min-w-0">
                    <dt class="text-sm text-muted-foreground">
                        {{ c.currentRouting }}
                    </dt>
                    <dd class="mt-1 text-sm">
                        <bdi>{{ name(routing.currentTopic) }}</bdi>
                    </dd>
                </div>
                <div class="min-w-0">
                    <dt class="text-sm text-muted-foreground">
                        {{ c.snapshotTopic }}
                    </dt>
                    <dd class="mt-1 text-sm">
                        <bdi>{{ name(routing.snapshotTopic) }}</bdi>
                    </dd>
                </div>
            </dl>
            <form
                v-if="routing.canCorrect"
                class="grid w-full min-w-0 gap-4"
                :aria-busy="form.processing"
                @submit.prevent="save"
            >
                <p
                    v-if="!routing.tracks.length"
                    class="text-sm text-muted-foreground"
                >
                    {{ c.noTopics }}
                </p>
                <div class="grid min-w-0 gap-2">
                    <Label :for="`routing-topic-${submissionId}`">{{
                        c.chooseTopic
                    }}</Label>
                    <Select v-model="form.track_id" :disabled="form.processing"
                        ><SelectTrigger
                            :id="`routing-topic-${submissionId}`"
                            class="w-full"
                            :aria-invalid="!!form.errors.track_id"
                            ><SelectValue
                                :placeholder="c.chooseTopic" /></SelectTrigger
                        ><SelectContent
                            ><SelectItem
                                v-for="track in routing.tracks"
                                :key="track.id"
                                :value="track.id"
                                >{{ name(track) }}</SelectItem
                            ></SelectContent
                        ></Select
                    >
                </div>
                <div class="grid min-w-0 gap-2">
                    <Label :for="`routing-reason-${submissionId}`">{{
                        c.routingReason
                    }}</Label
                    ><Textarea
                        :id="`routing-reason-${submissionId}`"
                        v-model="form.reason"
                        required
                        maxlength="2000"
                        :disabled="form.processing"
                        :aria-invalid="!!form.errors.reason"
                    />
                </div>
                <p
                    v-if="form.hasErrors"
                    role="alert"
                    class="text-sm text-destructive"
                >
                    {{
                        routingError
                            ? c.routingLocked
                            : form.errors.track_id
                              ? c.topicInvalid
                              : c.invalid
                    }}
                </p>
                <Button
                    :disabled="
                        form.processing || !form.track_id || !form.reason.trim()
                    "
                    class="h-auto min-h-10 max-w-full justify-self-start whitespace-normal"
                    ><PhSpinnerGap
                        v-if="form.processing"
                        class="size-4 animate-spin motion-reduce:animate-none"
                        aria-hidden="true"
                    />{{
                        form.processing ? c.loading : c.correctRouting
                    }}</Button
                >
            </form>
        </CardContent>
    </Card>
</template>
