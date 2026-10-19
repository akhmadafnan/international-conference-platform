<script setup lang="ts">
import { PhCheckCircle, PhMagnifyingGlass } from '@phosphor-icons/vue';
import { computed, onBeforeUnmount, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

type Candidate = {
    source: 'local' | 'ror';
    id: string | null;
    ror_uri: string | null;
    name: string;
    country_code: string | null;
    country: string | null;
    city: string | null;
};
const props = defineProps<{
    value: string;
    selectedId: string | null;
    disabled: boolean;
    inputId: string;
}>();
const emit = defineEmits<{
    (
        event: 'selected',
        item: { id: string; name: string; country_code: string | null },
    ): void;
    (event: 'manual', value: string): void;
}>();
const { t } = useI18n();
const query = ref(props.value);
const matches = ref<Candidate[]>([]);
const searching = ref(false);
const selecting = ref(false);
const unavailable = ref(false);
const failure = ref(false);
const selected = computed(() => !!props.selectedId);
let timer: ReturnType<typeof setTimeout> | undefined;
let requestId = 0;

watch(
    () => props.value,
    (next) => {
        if (next !== query.value) query.value = next;
    },
);
watch(query, (next) => {
    if (timer) clearTimeout(timer);
    matches.value = [];
    if (props.disabled || selected.value || next.trim().length < 3) return;
    const thisRequest = ++requestId;
    timer = setTimeout(async () => {
        searching.value = true;
        failure.value = false;
        try {
            const response = await fetch(
                '/dashboard/institutions/search?q=' +
                    encodeURIComponent(next.trim()),
                {
                    headers: { Accept: 'application/json' },
                    credentials: 'same-origin',
                },
            );
            if (!response.ok) throw new Error('ROR search failed');
            const data = await response.json();
            if (requestId !== thisRequest) return;
            matches.value = Array.isArray(data.items) ? data.items : [];
            unavailable.value = data.ror_available === false;
        } catch {
            if (requestId === thisRequest) {
                unavailable.value = true;
                failure.value = true;
            }
        } finally {
            if (requestId === thisRequest) searching.value = false;
        }
    }, 400);
});
function manual(value: string) {
    requestId++;
    query.value = value;
    matches.value = [];
    emit('manual', value);
}
async function choose(item: Candidate) {
    if (selecting.value || props.disabled) return;
    selecting.value = true;
    failure.value = false;
    try {
        let id = item.id;
        let name = item.name;
        let country_code = item.country_code;
        if (item.source === 'ror') {
            const csrf =
                document.cookie
                    .split('; ')
                    .find((x) => x.startsWith('XSRF-TOKEN='))
                    ?.slice('XSRF-TOKEN='.length) ?? '';
            const response = await fetch('/dashboard/institutions/resolve', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-XSRF-TOKEN': decodeURIComponent(csrf),
                },
                body: JSON.stringify({ ror_uri: item.ror_uri }),
            });
            if (!response.ok) throw new Error('ROR resolution failed');
            const resolved = await response.json();
            id = resolved.id;
            name = resolved.name;
            country_code = resolved.country_code;
        }
        if (id) {
            emit('selected', { id, name, country_code });
            query.value = name;
            matches.value = [];
            requestId++;
        }
    } catch {
        failure.value = true;
    } finally {
        selecting.value = false;
    }
}
onBeforeUnmount(() => {
    if (timer) clearTimeout(timer);
    requestId++;
});
</script>

<template>
    <div class="grid gap-2">
        <div class="relative">
            <PhMagnifyingGlass
                class="pointer-events-none absolute start-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground"
                aria-hidden="true"
            />
            <Input
                :id="inputId"
                :model-value="query"
                :disabled="disabled || selected"
                :placeholder="t('submissionWizard.rorSearch')"
                class="ps-9"
                maxlength="255"
                required
                autocomplete="off"
                @update:model-value="(v) => manual(String(v))"
            />
        </div>
        <div
            v-if="selected"
            class="flex flex-wrap items-center gap-2 text-xs text-primary"
        >
            <PhCheckCircle class="size-4" aria-hidden="true" />
            <span>{{ t('submissionWizard.rorSelected') }}</span>
            <Button
                v-if="!disabled"
                size="sm"
                type="button"
                variant="outline"
                @click="manual(query)"
                >{{ t('submissionWizard.changeInstitution') }}</Button
            >
        </div>
        <div
            v-if="!selected && !disabled && query.trim().length >= 3"
            class="grid gap-2"
        >
            <p
                v-if="searching"
                role="status"
                class="text-xs text-muted-foreground"
            >
                {{ t('submissionWizard.rorSearching') }}
            </p>
            <ul
                v-if="matches.length"
                :aria-label="t('submissionWizard.rorSearch')"
                class="max-h-56 overflow-y-auto border border-border bg-card p-1"
            >
                <li
                    v-for="item in matches"
                    :key="item.ror_uri ?? item.id ?? item.name"
                >
                    <Button
                        type="button"
                        variant="ghost"
                        class="h-auto! w-full justify-start p-3 text-start whitespace-normal"
                        :disabled="selecting"
                        @click="choose(item)"
                    >
                        <span class="flex min-w-0 flex-col gap-1">
                            <span class="text-sm font-medium">{{
                                item.name
                            }}</span>
                            <span class="text-xs text-muted-foreground"
                                >{{
                                    [item.city, item.country, item.country_code]
                                        .filter(Boolean)
                                        .join(' · ')
                                }}
                                ·
                                {{
                                    item.source === 'ror'
                                        ? 'ROR'
                                        : t('submissionWizard.savedInstitution')
                                }}</span
                            >
                        </span>
                    </Button>
                </li>
            </ul>
            <p
                v-if="unavailable"
                role="status"
                class="text-xs text-muted-foreground"
            >
                {{ t('submissionWizard.rorUnavailable') }}
            </p>
            <p v-if="failure" role="alert" class="text-xs text-destructive">
                {{ t('submissionWizard.rorError') }}
            </p>
            <p class="text-xs text-muted-foreground">
                {{ t('submissionWizard.manualFallback') }}
            </p>
        </div>
    </div>
</template>
