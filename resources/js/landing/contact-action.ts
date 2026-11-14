// The supplied contact form is a visual demo; delivery is not configured yet.
export function useAction() {
    return {
        isExecuting: false,
        hasSucceeded: false,
        execute: () => window.alert('Demo form. Contact delivery has not been configured yet.'),
    };
}
