import React, { useEffect, useRef, useState } from 'react';
import { createRoot } from 'react-dom/client';
import { ArrowLeft, ArrowRight, Check, CheckCircle2, FileText, LockKeyhole, Moon, Sun, Save, UploadCloud } from 'lucide-react';
import { ThemeProvider, useTheme } from 'next-themes';
import '@fontsource/inter/latin.css';
import '@/styles/globals.css';
import '../landing/fonts.css';
import './onboarding.css';

type Answers = Record<string, string | string[]>;
type Field = { key: string; label: string; type: string; options?: string[]; options_from?: string; when?: string[]; interface?: boolean; public?: boolean; help?: string; required?: boolean; max?: number; maxLength: number; maxLinks: number; highlight?: boolean };
type Attachment = { id: number; category: string; original_name: string; size: number };
const data = JSON.parse(document.getElementById('onboarding-data')!.textContent!);
const sections: { title: string; intro: string; fields: Field[] }[] = data.sections;
const csrf = document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')!.content;
const visible = (field: Field, answers: Answers) => !(field.interface && answers.has_interface === 'No') &&
    !(field.public && answers.public_site !== 'Sí') && (!field.when || [answers[field.when[0]]].flat().includes(field.when[1]));
const accepts = '.jpg,.jpeg,.png,.webp,.gif,.svg,.pdf,.txt,.csv,.xls,.xlsx,.doc,.docx,.ppt,.pptx,.mp4,.mp3';

function validateFields(fields: Field[], answers: Answers, required: boolean) {
    const issues: Record<string, string[]> = {};
    for (const field of fields.filter(field => visible(field, answers))) {
        const value = answers[field.key];
        const empty = !value || (typeof value === 'string' ? !value.trim() : !value.length);
        if (required && field.required && empty) issues[field.key] = ['Completa este campo para continuar.'];
        if (empty) continue;
        if (typeof value === 'string' && [...value].length > field.maxLength) issues[field.key] = [`Puedes escribir hasta ${field.maxLength} caracteres. Puedes adjuntar un documento con más detalles.`];
        if (field.type === 'email' && typeof value === 'string' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value.trim())) issues[field.key] = ['Revisa tu correo. Por ejemplo: nombre@empresa.com.'];
        if (field.type === 'date' && typeof value === 'string') {
            const date = new Date(value + 'T00:00:00Z');
            if (!/^\d{4}-\d{2}-\d{2}$/.test(value) || Number.isNaN(date.getTime()) || date.toISOString().slice(0, 10) !== value) issues[field.key] = ['Selecciona una fecha válida en el calendario o deja el campo vacío si aún no la conoces.'];
        }
        if (field.type === 'links' && Array.isArray(value)) {
            const links = value.map(link => link.trim()).filter(Boolean);
            if (links.length > field.maxLinks) issues[field.key] = [`Puedes compartir hasta ${field.maxLinks} enlaces.`];
            links.forEach((link, index) => {
                try {
                    const normalized = link.startsWith('//') ? 'https:' + link : /^[a-z][a-z0-9+.-]*:/i.test(link) ? link : 'https://' + link;
                    const url = new URL(normalized);
                    if (!['http:', 'https:'].includes(url.protocol) || /\s/.test(link) || [...link].length > field.maxLength) throw new Error();
                } catch { issues[`${field.key}.${index}`] = [`Revisa el enlace ${index + 1}. Por ejemplo: facebook.com/tuempresa (hasta ${field.maxLength} caracteres).`]; }
            });
        }
    }
    return issues;
}

async function api(path: string, method: string, body?: object | FormData) {
    const response = await fetch(data.endpoint + path, { method, headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrf,
        ...(body instanceof FormData ? {} : { 'Content-Type': 'application/json' }) }, body: body instanceof FormData ? body : body ? JSON.stringify(body) : undefined })
        .catch(() => { throw new Error('No pudimos conectar. Revisa tu conexión e intenta nuevamente; tus respuestas siguen en pantalla.'); });
    const result = await response.json().catch(() => ({}));
    if (!response.ok) {
        const message = response.status === 419 ? 'Tu sesión expiró. Guarda tus respuestas abiertas y recarga la página para continuar.' :
            response.status === 404 ? 'Este enlace venció o fue desactivado. Contacta a nuestro equipo.' :
            response.status === 413 ? 'El archivo supera el tamaño permitido por el servidor.' :
            response.status === 429 ? 'Espera un momento antes de intentarlo nuevamente.' : response.status >= 500 ? 'No pudimos guardar en este momento. Tus respuestas siguen en pantalla; intenta nuevamente.' : result.message || 'No pudimos guardar. Revisa tu conexión e intenta nuevamente.';
        throw Object.assign(new Error(message), { errors: result.errors || {} });
    }
    return result;
}

function App() {
    const { resolvedTheme, setTheme } = useTheme();
    const lightsOn = resolvedTheme === 'light';
    const [answers, setAnswers] = useState<Answers>(data.answers || {});
    const [step, setStep] = useState(0);
    const [files, setFiles] = useState<Attachment[]>(data.attachments || []);
    const [submitted, setSubmitted] = useState(data.submitted);
    const [busy, setBusy] = useState(false);
    const [uploading, setUploading] = useState(false);
    const [status, setStatus] = useState('Puedes completar el formulario a tu ritmo.');
    const [error, setError] = useState('');
    const [errors, setErrors] = useState<Record<string, string[]>>({});
    const changed = useRef(false);
    const current = useRef(answers);
    const chain = useRef<Promise<unknown>>(Promise.resolve());
    const heading = useRef<HTMLHeadingElement>(null);
    const locked = useRef(false);
    const section = sections[step];

    function save(snapshot: Answers, submit = false) {
        const operation = chain.current.catch(() => {}).then(async () => {
            if (locked.current) return;
            setStatus(submit ? 'Enviando tu información…' : 'Guardando borrador…');
            const result = await api('', 'PUT', { answers: snapshot, submit });
            if (current.current === snapshot) changed.current = false;
            setStatus('Borrador guardado. Puedes regresar con este mismo enlace.');
            if (result.submitted) { locked.current = true; setSubmitted(true); }
            return result;
        });
        chain.current = operation;
        return operation;
    }
    useEffect(() => {
        if (!changed.current || submitted || busy) return;
        const timer = setTimeout(() => save(answers).catch((e: any) => {
            if (current.current !== answers) return;
            setErrors(e.errors || {});
            setStatus(Object.keys(e.errors || {}).length
                ? 'Hay un campo pendiente de corregir. Tus respuestas siguen en pantalla; usa «Guardar y continuar después» para revisarlo.'
                : 'Cambios sin guardar. Usa «Guardar y continuar después» para reintentar.');
        }), 1400);
        return () => clearTimeout(timer);
    }, [answers, submitted, busy]);
    useEffect(() => {
        const beforeUnload = (event: BeforeUnloadEvent) => { if (changed.current) { event.preventDefault(); event.returnValue = ''; } };
        window.addEventListener('beforeunload', beforeUnload);
        return () => window.removeEventListener('beforeunload', beforeUnload);
    }, []);
    useEffect(() => { heading.current?.focus(); }, [step, submitted]);

    function update(key: string, value: string | string[]) {
        const next = { ...current.current, [key]: value };
        if (key === 'features') {
            next.required_features = ((next.required_features || []) as string[]).filter(v => value.includes(v));
            next.optional_features = ((next.optional_features || []) as string[]).filter(v => value.includes(v));
        }
        if (key === 'required_features') next.optional_features = ((next.optional_features || []) as string[]).filter(v => !value.includes(v));
        if (key === 'optional_features') next.required_features = ((next.required_features || []) as string[]).filter(v => !value.includes(v));
        changed.current = true; current.current = next; setAnswers(next);
        setErrors(previous => Object.fromEntries(Object.entries(previous).filter(([name]) => name.split('.')[0] !== key)));
        setStatus('Cambios pendientes de guardar…');
    }
    function go(next: number) { setStep(next); setError(''); window.scrollTo({ top: 0, behavior: 'smooth' }); }

    async function upload(category: string, selection: FileList | File[]) {
        if (uploading || busy) return;
        if (files.length + selection.length > data.maxFiles) { setError(`Puedes compartir hasta ${data.maxFiles} archivos en total.`); return; }
        const extensions = (category === 'logo' ? '.jpg,.jpeg,.png,.webp,.gif,.svg,.pdf' : accepts).split(',');
        for (const file of Array.from(selection)) {
            if (!extensions.includes('.' + file.name.split('.').pop()?.toLowerCase())) { setError(`«${file.name}» tiene un formato que no podemos recibir aquí. Formatos permitidos: ${extensions.join(', ')}.`); return; }
            if (!file.size) { setError(`«${file.name}» está vacío. Revisa el archivo y vuelve a seleccionarlo.`); return; }
            if (file.size > data.maxFileKb * 1024) { setError(`«${file.name}» supera el límite de ${data.maxFileKb / 1024} MB. Comparte una versión más pequeña.`); return; }
        }
        setUploading(true); setError('');
        try {
            for (const file of Array.from(selection)) {
                if (file.size > data.maxFileKb * 1024) throw new Error(`«${file.name}» supera el límite de ${data.maxFileKb / 1024} MB.`);
                const body = new FormData(); body.append('category', category); body.append('file', file);
                const uploaded = await api('/files', 'POST', body);
                setFiles(previous => [...previous, uploaded]);
            }
        } catch (e: any) { setError(Object.values(e.errors || {}).flat().join(' ') || e.message); }
        finally { setUploading(false); }
    }
    async function remove(file: Attachment) {
        setUploading(true); setError('');
        try { await api('/files/' + file.id, 'DELETE'); setFiles(previous => previous.filter(item => item.id !== file.id)); }
        catch (e: any) { setError(e.message); }
        finally { setUploading(false); }
    }
    async function finish(submit: boolean) {
        if (busy || uploading) return;
        const issues = validateFields(sections.flatMap(section => section.fields), current.current, submit);
        if (Object.keys(issues).length) {
            setErrors(issues); setError('Revisa los campos señalados. Tus respuestas siguen aquí.');
            setStep(sections.findIndex(section => section.fields.some(field => Object.keys(issues).some(key => key.split('.')[0] === field.key))));
            return;
        }
        setBusy(true); setError(''); setErrors({});
        try { await save(current.current, submit); }
        catch (e: any) {
            setError(Object.keys(e.errors || {}).length ? 'Revisa los campos señalados. Tus respuestas siguen aquí; no necesitas empezar de nuevo.' : e.message); setErrors(e.errors || {});
            const invalidStep = sections.findIndex(section => section.fields.some(field => Object.keys(e.errors || {}).some(key => key.split('.')[0] === field.key)));
            if (invalidStep >= 0) setStep(invalidStep);
            setStatus('No se guardaron los últimos cambios. Intenta nuevamente.');
        } finally { setBusy(false); }
    }

    function renderField(field: Field) {
        if (!visible(field, answers)) return null;
        const value = answers[field.key] || (['multi', 'links'].includes(field.type) ? [] : '');
        const options = field.options_from ? (answers[field.options_from] || []) as string[] : field.options || [];
        const fieldErrors = Object.entries(errors).filter(([key]) => key.split('.')[0] === field.key).flatMap(([, value]) => value);
        const links = field.type === 'links' ? (value as string[]).filter(link => link.trim()) : [];
        if (links.length > field.maxLinks) fieldErrors.push(`Puedes compartir hasta ${field.maxLinks} enlaces. Conserva los más importantes.`);
        if (links.some(link => [...link.trim()].length > field.maxLength)) fieldErrors.push(`Cada enlace puede tener hasta ${field.maxLength} caracteres.`);
        const inputProps = { id: field.key, 'aria-label': field.label, 'aria-describedby': [field.help && field.key + '-help', field.key + '-limit', fieldErrors.length && field.key + '-errors'].filter(Boolean).join(' '), 'aria-invalid': !!fieldErrors.length, disabled: busy };
        return <div className={'ob-field ' + (field.highlight ? 'ob-highlight' : '')} key={field.key}>
            <label className="ob-label" htmlFor={field.key}>{field.label}{field.required && <span className="ob-required"> *</span>}</label>
            {field.help && <p className="ob-help" id={field.key + '-help'}>{field.help}</p>}
            {['choice', 'multi'].includes(field.type) ? <div className="ob-options" role="group" aria-label={field.label}>
                {options.map(option => {
                    const selected = field.type === 'multi' ? (value as string[]).includes(option) : value === option;
                    return <label className={'ob-option ' + (selected ? 'selected' : '')} key={option}>
                        <input type={field.type === 'multi' ? 'checkbox' : 'radio'} name={field.key} checked={selected} disabled={busy || (!selected && field.type === 'multi' && !!field.max && (value as string[]).length >= field.max)}
                            onChange={() => update(field.key, field.type === 'multi' ? selected ? (value as string[]).filter(v => v !== option) : [...value as string[], option] : option)} />
                        <span>{option}</span>{selected && <Check size={16} aria-hidden="true" />}
                    </label>;
                })}
                {!options.length && <p className="ob-help">Selecciona primero las funciones que necesitas.</p>}
            </div> : field.type === 'files' ? <div>
                <div className="ob-drop" onDragOver={e => e.preventDefault()} onDrop={e => { e.preventDefault(); upload(field.key, e.dataTransfer.files); }}>
                    <UploadCloud size={28} aria-hidden="true" /><p>Arrastra tus archivos o selecciónalos</p>
                    <input id={field.key} aria-label={field.label} type="file" multiple accept={field.key === 'logo' ? '.jpg,.jpeg,.png,.webp,.gif,.svg,.pdf' : accepts} disabled={busy || uploading}
                        onChange={e => { if (e.target.files) upload(field.key, e.target.files); e.target.value = ''; }} />
                    <small>Hasta {data.maxFileKb / 1024} MB por archivo · {data.maxFiles} archivos en total. No compartas contraseñas.</small>
                </div>
                {files.filter(file => file.category === field.key).map(file => <div className="ob-file" key={file.id}><FileText size={17} /><span>{file.original_name} <small>{Math.ceil(file.size / 1024)} KB</small></span><button type="button" disabled={busy || uploading} onClick={() => remove(file)} aria-label={'Quitar ' + file.original_name}>Quitar</button></div>)}
            </div> : ['textarea', 'links'].includes(field.type) ? <textarea {...inputProps} rows={field.type === 'links' ? 3 : 4} maxLength={field.type === 'links' ? undefined : field.maxLength}
                placeholder={field.type === 'links' ? 'facebook.com/tuempresa\nwww.ejemplo.com' : undefined}
                value={field.type === 'links' ? (value as string[]).join('\n') : value as string} onChange={e => update(field.key, field.type === 'links' ? e.target.value.split('\n') : e.target.value)} /> :
                <input {...inputProps} type={field.type} maxLength={field.maxLength} value={value as string} onChange={e => update(field.key, e.target.value)} />}
            {field.type === 'links' ? <p className="ob-help" id={field.key + '-limit'}>{links.length} de {field.maxLinks} enlaces · Uno por renglón, hasta {field.maxLength} caracteres por enlace. Puedes omitir https://.</p> :
                ['text', 'textarea', 'email', 'tel'].includes(field.type) && <p className="ob-help" id={field.key + '-limit'}>{[...(value as string)].length} / {field.maxLength} caracteres{(value as string).length >= field.maxLength ? ' · Llegaste al límite. Puedes adjuntar un documento con más detalles.' : ''}</p>}
            <div id={field.key + '-errors'} role={fieldErrors.length ? 'alert' : undefined}>{fieldErrors.map((error, index) => <p className="ob-field-error" key={index}>{error}</p>)}</div>
        </div>;
    }

    return <div className="ob-page">
        <header className="ob-header"><div className="ob-brand"><img className="ob-logo ob-logo-light" src={data.logos.light} alt="" width={64} height={64} /><img className="ob-logo ob-logo-dark" src={data.logos.dark} alt="" width={64} height={64} /><span>NEXUS<span className="ob-brand-sub">DIGITAL DB</span></span></div><div className="ob-header-actions"><span className="ob-private"><LockKeyhole size={18} aria-hidden="true" /> Espacio privado de tu proyecto</span><button className="ob-theme-toggle" type="button" aria-pressed={lightsOn} aria-label="Encender las luces" onClick={() => setTheme(lightsOn ? 'dark' : 'light')}>{lightsOn ? <Moon size={20} aria-hidden="true" /> : <Sun size={20} aria-hidden="true" />}<span>{lightsOn ? 'Apagar las luces' : 'Encender las luces'}</span></button></div></header>
        {submitted ? <main className="ob-success"><CheckCircle2 size={52} /><p className="ob-eyebrow">EL PRIMER PASO ESTÁ HECHO</p><h1 ref={heading} tabIndex={-1}>¡Gracias! Ya tenemos la información inicial de tu proyecto.</h1><p>Nuestro equipo la revisará y se pondrá en contacto contigo para definir los siguientes pasos.</p><div className="ob-success-project">{data.name}<small>{data.company}</small></div><p className="ob-help">Si necesitas hacer una corrección, comunícate con tu contacto de NEXUS DIGITAL DB.</p></main> : <>
            <div className="ob-intro"><p className="ob-eyebrow">TU VISIÓN. NUESTRO SIGUIENTE PROYECTO.</p><h1>Cuéntanos sobre<br /><span>tu proyecto.</span></h1><p>No necesitas conocimientos técnicos. Cuéntanos cómo trabajas y qué te gustaría mejorar; nosotros nos encargamos de convertirlo en una solución.</p><div className="ob-project-tag">{data.company}<span>/</span>{data.name}</div></div>
            <main className="ob-layout">
                <aside className="ob-sidebar"><p className="ob-eyebrow">CONSTRUYAMOS EL PUNTO DE PARTIDA</p><nav aria-label="Secciones del formulario">{sections.map((section, index) => <button type="button" key={section.title} className={index === step ? 'active' : ''} aria-current={index === step ? 'step' : undefined} onClick={() => go(index)} disabled={busy}><span>{String(index + 1).padStart(2, '0')}</span>{section.title}{index < step && <Check size={14} />}</button>)}</nav><div className="ob-sidebar-note"><LockKeyhole size={18} /><p>Tu información nos ayuda a preparar una primera reunión más productiva.</p></div></aside>
                <div className="ob-panel">
                    <div className="ob-progress-label"><span>Paso {step + 1} de {sections.length}</span><span>{Math.round((step + 1) / sections.length * 100)}%</span></div>
                    <div className="ob-progress" role="progressbar" aria-label="Avance por secciones" aria-valuenow={step + 1} aria-valuemin={0} aria-valuemax={sections.length}><span style={{ width: (step + 1) / sections.length * 100 + '%' }} /></div>
                    <div className="ob-section-heading"><p className="ob-eyebrow">{String(step + 1).padStart(2, '0')} / TU PROYECTO</p><h2 ref={heading} tabIndex={-1}>{section.title}</h2><p>{section.intro}</p><small>Los campos con * son necesarios para enviar. Los demás puedes dejarlos vacíos. En respuestas de texto puedes escribir «no aplica» o «no estoy seguro».</small></div>
                    <form onSubmit={e => {
                        e.preventDefault();
                        const issues = validateFields(section.fields, current.current, true);
                        if (Object.keys(issues).length) { setErrors(issues); setError('Revisa los campos señalados antes de continuar. Tus respuestas siguen aquí.'); return; }
                        if (step < sections.length - 1) go(step + 1); else finish(true);
                    }} noValidate>
                        {section.fields.map(renderField)}
                        {step === sections.length - 1 && <div className="ob-summary"><h3>Tu proyecto, de un vistazo</h3>{['company', 'contact_name', 'contact_email', 'project_types', 'main_problem', 'goal_one', 'deadline', 'budget_range'].map(key => {
                            const field = sections.flatMap(s => s.fields).find(f => f.key === key)!;
                            return <div key={key}><strong>{field.label}</strong><p>{Array.isArray(answers[key]) ? (answers[key] as string[]).join(', ') : answers[key] || 'Sin especificar'}</p></div>;
                        })}<p>{files.length} archivos adjuntos.</p><small>Puedes volver a cualquier sección para hacer cambios antes de enviar.</small></div>}
                        {error && <div className="ob-error" role="alert">{error}</div>}
                        {uploading && <p role="status">Procesando archivos…</p>}
                        <div className="ob-navigation"><button type="button" className="ob-secondary" onClick={() => go(step - 1)} disabled={!step || busy}><ArrowLeft size={16} /> Anterior</button><button type="submit" className="ob-primary" disabled={busy || uploading}>{busy ? 'Guardando…' : step === sections.length - 1 ? 'Enviar información del proyecto' : 'Continuar'}<ArrowRight size={16} /></button></div>
                    </form>
                    <div className="ob-save"><div className="ob-save-copy"><strong>¿Necesitas una pausa?</strong><p>Puedes regresar con este mismo enlace.</p></div><button type="button" disabled={busy || uploading} onClick={() => finish(false)}><Save size={18} aria-hidden="true" />Guardar y continuar después</button><p className="ob-save-status" role="status" aria-live="polite">{status}</p></div>
                </div>
            </main>
        </>}
        <footer className="ob-footer"><span>NEXUS DIGITAL DB</span><span>Ingeniería de software alrededor de tu empresa.</span></footer>
    </div>;
}

createRoot(document.getElementById('onboarding-root')!).render(<ThemeProvider attribute="class" defaultTheme="dark" enableSystem={false} disableTransitionOnChange><App /></ThemeProvider>);
