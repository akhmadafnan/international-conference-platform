import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

export function useWorkspaceLyra() {
    const page = usePage();
    const isAdmin = computed(() => page.component.startsWith('admin/'));
    const isParticipant = computed(
        () =>
            page.component === 'Dashboard' ||
            page.component === 'registration/Show' ||
            page.component === 'workspace/RoleOverview' ||
            page.component.startsWith('academic/') ||
            page.component.startsWith('submissions/'),
    );
    const isLyra = computed(() => isAdmin.value || isParticipant.value);

    return { isAdmin, isParticipant, isLyra };
}
