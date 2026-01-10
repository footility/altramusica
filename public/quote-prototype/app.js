// Prototipo preventivo parametrico - Altramusica
// Richiede data.js caricato prima di questo file.

(function () {
    const PHASE_KEYS = ["unassigned", "phase1", "phase2", "phase3", "phase4"];

    const state = {
        items: [], // { ...functionDef, phaseKey }
        draggingId: null,
        params: {
            ratePerDay: 300,
            hoursPerDay: 6,
            hoursPerPoint: 0.15,
            aiFactor: 0.15 // 0..0.30
        }
    };

    // --- Inizializzazione ---

    function init() {
        // Inizializza dati
        state.items = window.FUNCTIONS.map(fn => ({
            ...fn,
            phaseKey: fn.phaseKey || "unassigned"
        }));

        // Inizializza filtri macro-area
        const macroSelect = document.getElementById("macroAreaFilter");
        window.MACRO_AREAS.forEach(area => {
            const opt = document.createElement("option");
            opt.value = area;
            opt.textContent = area;
            macroSelect.appendChild(opt);
        });

        // Aggancia eventi
        document.getElementById("macroAreaFilter").addEventListener("change", renderAll);
        document.getElementById("searchFilter").addEventListener("input", renderAll);

        document.getElementById("ratePerDay").addEventListener("input", onParamsChange);
        document.getElementById("hoursPerDay").addEventListener("input", onParamsChange);
        document.getElementById("hoursPerPoint").addEventListener("input", onParamsChange);
        document.getElementById("aiFactor").addEventListener("input", onAiFactorChange);

        document.getElementById("exportMarkdownBtn").addEventListener("click", exportMarkdown);

        // Drag & drop: droppable lists
        document.querySelectorAll(".card-list.droppable").forEach(list => {
            list.addEventListener("dragover", onDragOverList);
            list.addEventListener("dragleave", onDragLeaveList);
            list.addEventListener("drop", onDropOnList);
        });

        // Parametri iniziali da DOM
        syncParamsFromInputs();

        renderAll();
    }

    function syncParamsFromInputs() {
        const ratePerDay = parseFloat(document.getElementById("ratePerDay").value) || 0;
        const hoursPerDay = parseFloat(document.getElementById("hoursPerDay").value) || 1;
        const hoursPerPoint = parseFloat(document.getElementById("hoursPerPoint").value) || 0.01;
        const aiSlider = document.getElementById("aiFactor");
        const aiFactor = (parseInt(aiSlider.value, 10) || 0) / 100;
        state.params.ratePerDay = ratePerDay;
        state.params.hoursPerDay = hoursPerDay;
        state.params.hoursPerPoint = hoursPerPoint;
        state.params.aiFactor = aiFactor;
        document.getElementById("aiFactorLabel").textContent = Math.round(aiFactor * 100) + "%";
    }

    function onParamsChange() {
        syncParamsFromInputs();
        renderSummaryAndWarnings();
    }

    function onAiFactorChange() {
        syncParamsFromInputs();
        renderAll();
    }

    // --- Render ---

    function renderAll() {
        renderLibrary();
        renderPhases();
        renderSummaryAndWarnings();
    }

    function getFilteredItemsForLibrary() {
        const macroFilter = document.getElementById("macroAreaFilter").value || "";
        const search = (document.getElementById("searchFilter").value || "").toLowerCase();
        return state.items.filter(item => {
            if (macroFilter && item.macroArea !== macroFilter) return false;
            if (search) {
                const hay = (item.title + " " + item.macroArea + " " + (item.tags || []).join(" ")).toLowerCase();
                if (!hay.includes(search)) return false;
            }
            return true;
        });
    }

    function renderLibrary() {
        const container = document.getElementById("libraryList");
        container.innerHTML = "";
        const items = getFilteredItemsForLibrary();
        if (!items.length) {
            const p = document.createElement("p");
            p.className = "empty-text";
            p.textContent = "Nessuna funzione da mostrare con i filtri attuali.";
            container.appendChild(p);
            return;
        }
        items.forEach(item => container.appendChild(createCardElement(item)));
    }

    function renderPhases() {
        const byPhase = {
            phase1: document.getElementById("phase1List"),
            phase2: document.getElementById("phase2List"),
            phase3: document.getElementById("phase3List"),
            phase4: document.getElementById("phase4List")
        };
        Object.values(byPhase).forEach(c => (c.innerHTML = ""));
        state.items.forEach(item => {
            if (item.phaseKey && item.phaseKey !== "unassigned" && byPhase[item.phaseKey]) {
                byPhase[item.phaseKey].appendChild(createCardElement(item));
            }
        });
    }

    function createCardElement(item) {
        const card = document.createElement("article");
        card.className = "card";
        card.draggable = true;
        card.dataset.id = item.id;

        card.addEventListener("dragstart", onDragStartCard);
        card.addEventListener("dragend", onDragEndCard);

        const header = document.createElement("div");
        header.className = "card-header";

        const title = document.createElement("div");
        title.className = "card-title";
        title.textContent = item.title;

        const macroBadge = document.createElement("span");
        macroBadge.className = "badge badge-macro";
        macroBadge.textContent = item.macroArea;

        header.appendChild(title);
        header.appendChild(macroBadge);

        const tagsRow = document.createElement("div");
        tagsRow.className = "card-tags";
        (item.tags || []).forEach(tag => {
            const b = document.createElement("span");
            b.className = "badge badge-tag";
            b.textContent = tag;
            tagsRow.appendChild(b);
        });

        const meta = document.createElement("div");
        meta.className = "card-meta";

        const effPoints = getEffectivePoints(item);
        const phaseLabel = phaseKeyToLabel(item.phaseKey);
        meta.innerHTML = `
            <span>${item.groupKey || ""}</span>
            <span>${effPoints.toFixed(1)} pt · ${phaseLabel}</span>
        `;

        card.appendChild(header);
        card.appendChild(tagsRow);
        if (item.notes) {
            const notesEl = document.createElement("div");
            notesEl.className = "card-notes";
            notesEl.style.fontSize = "0.7rem";
            notesEl.style.color = "#9ca3af";
            notesEl.textContent = item.notes;
            card.appendChild(notesEl);
        }
        card.appendChild(meta);

        const depIssues = getDependencyIssues(item);
        if (depIssues.hardIssues.length || depIssues.softIssues.length) {
            const warn = document.createElement("div");
            warn.className = "card-warning";
            const msgSpan = document.createElement("span");
            msgSpan.textContent = buildDepWarningLabel(depIssues);
            warn.appendChild(msgSpan);

            if (depIssues.hardIssues.length) {
                const btn = document.createElement("button");
                btn.type = "button";
                btn.textContent = "Auto-fix";
                btn.addEventListener("click", function (e) {
                    e.stopPropagation();
                    handleAutoFix(item.id);
                });
                warn.appendChild(btn);
            }

            card.appendChild(warn);
        }

        return card;
    }

    function phaseKeyToLabel(phaseKey) {
        switch (phaseKey) {
            case "phase1":
                return "Fase 1";
            case "phase2":
                return "Fase 2";
            case "phase3":
                return "Fase 3";
            case "phase4":
                return "Fase 4";
            default:
                return "Non assegnata";
        }
    }

    // --- Drag & drop ---

    function onDragStartCard(e) {
        const id = e.currentTarget.dataset.id;
        state.draggingId = id;
        e.dataTransfer.setData("text/plain", id);
        e.dataTransfer.effectAllowed = "move";
        e.currentTarget.classList.add("dragging");
    }

    function onDragEndCard(e) {
        state.draggingId = null;
        e.currentTarget.classList.remove("dragging");
        document.querySelectorAll(".card-list.droppable").forEach(list =>
            list.classList.remove("drag-over")
        );
    }

    function onDragOverList(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = "move";
        e.currentTarget.classList.add("drag-over");
    }

    function onDragLeaveList(e) {
        e.currentTarget.classList.remove("drag-over");
    }

    function onDropOnList(e) {
        e.preventDefault();
        const list = e.currentTarget;
        const phase = list.dataset.phase;
        const id = e.dataTransfer.getData("text/plain") || state.draggingId;
        list.classList.remove("drag-over");
        if (!id) return;
        const idx = state.items.findIndex(i => i.id === id);
        if (idx === -1) return;
        state.items[idx].phaseKey = phase;
        renderAll();
    }

    // --- Calcoli punti / giorni / costi ---

    function getPhaseIndex(phaseKey) {
        return PHASE_KEYS.indexOf(phaseKey || "unassigned");
    }

    function isMechanical(item) {
        return (item.tags || []).includes("meccanica");
    }

    function getEffectivePoints(item) {
        const base = Number(item.effortPoints) || 0;
        if (!isMechanical(item)) return base;
        const factor = state.params.aiFactor || 0;
        return base * (1 - factor);
    }

    function computePhaseSummary() {
        const summary = {
            phase1: { points: 0 },
            phase2: { points: 0 },
            phase3: { points: 0 },
            phase4: { points: 0 }
        };
        state.items.forEach(item => {
            if (
                item.phaseKey &&
                item.phaseKey !== "unassigned" &&
                summary[item.phaseKey]
            ) {
                summary[item.phaseKey].points += getEffectivePoints(item);
            }
        });

        const { hoursPerPoint, hoursPerDay, ratePerDay } = state.params;
        let totalPoints = 0;
        let totalDays = 0;
        let totalCost = 0;

        Object.keys(summary).forEach(phaseKey => {
            const phase = summary[phaseKey];
            const hours = phase.points * hoursPerPoint;
            const days = hoursPerDay > 0 ? hours / hoursPerDay : 0;
            const cost = days * ratePerDay;
            phase.hours = hours;
            phase.days = days;
            phase.cost = cost;
            totalPoints += phase.points;
            totalDays += days;
            totalCost += cost;
        });

        return {
            summary,
            totalPoints,
            totalDays,
            totalCost
        };
    }

    function renderSummaryAndWarnings() {
        const { summary, totalPoints, totalDays, totalCost } = computePhaseSummary();
        const body = document.getElementById("phaseSummaryBody");
        body.innerHTML = "";
        const rows = [
            { key: "phase1", label: "Fase 1" },
            { key: "phase2", label: "Fase 2" },
            { key: "phase3", label: "Fase 3" },
            { key: "phase4", label: "Fase 4" }
        ];

        rows.forEach(r => {
            const phase = summary[r.key];
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td>${r.label}</td>
                <td>${phase.points.toFixed(1)}</td>
                <td>${phase.days.toFixed(1)}</td>
                <td>${Math.round(phase.cost)}</td>
            `;
            body.appendChild(tr);
        });

        document.getElementById("totalPoints").textContent = totalPoints.toFixed(1);
        document.getElementById("totalDays").textContent = totalDays.toFixed(1);
        document.getElementById("totalCost").textContent = Math.round(totalCost);

        renderWarningsList();
    }

    // --- Dipendenze ---

    function getDependencyIssues(item) {
        const hardIssues = [];
        const softIssues = [];
        const itemPhaseIndex = getPhaseIndex(item.phaseKey);

        (item.dependencies || []).forEach(dep => {
            const depItem = state.items.find(i => i.id === dep.id);
            if (!depItem) return;
            const depPhaseIndex = getPhaseIndex(depItem.phaseKey);
            const violates =
                dep.type === "hard" &&
                (depPhaseIndex === -1 ||
                    depPhaseIndex === 0 || // unassigned
                    depPhaseIndex > itemPhaseIndex);
            const softWarn =
                dep.type === "soft" &&
                (depPhaseIndex === -1 || depPhaseIndex === 0 || depPhaseIndex > itemPhaseIndex);

            if (violates) {
                hardIssues.push({
                    depId: dep.id,
                    depTitle: depItem.title,
                    depPhaseKey: depItem.phaseKey
                });
            } else if (softWarn) {
                softIssues.push({
                    depId: dep.id,
                    depTitle: depItem.title,
                    depPhaseKey: depItem.phaseKey
                });
            }
        });

        return { hardIssues, softIssues };
    }

    function buildDepWarningLabel(depIssues) {
        const parts = [];
        if (depIssues.hardIssues.length) {
            parts.push("Dipendenze obbligatorie non rispettate");
        }
        if (depIssues.softIssues.length) {
            parts.push("Consigliato includere alcune funzioni correlate");
        }
        return parts.join(" · ");
    }

    function handleAutoFix(itemId) {
        const item = state.items.find(i => i.id === itemId);
        if (!item) return;
        const itemPhaseIdx = getPhaseIndex(item.phaseKey);
        if (itemPhaseIdx <= 0) return; // non assegnata o impossibile

        (item.dependencies || [])
            .filter(d => d.type === "hard")
            .forEach(dep => {
                const depItem = state.items.find(i => i.id === dep.id);
                if (!depItem) return;
                const depPhaseIdx = getPhaseIndex(depItem.phaseKey);
                if (depPhaseIdx === 0 || depPhaseIdx > itemPhaseIdx) {
                    // sposta dipendenza nella stessa fase della funzione
                    depItem.phaseKey = item.phaseKey;
                }
            });

        renderAll();
    }

    function renderWarningsList() {
        const container = document.getElementById("warningsList");
        container.innerHTML = "";
        const issues = [];
        state.items.forEach(item => {
            if (!item.phaseKey || item.phaseKey === "unassigned") return;
            const di = getDependencyIssues(item);
            if (di.hardIssues.length || di.softIssues.length) {
                issues.push({ item, di });
            }
        });

        if (!issues.length) {
            const p = document.createElement("p");
            p.className = "empty-text";
            p.textContent = "Nessuna anomalia di dipendenza rilevata.";
            container.appendChild(p);
            return;
        }

        issues.forEach(entry => {
            const { item, di } = entry;
            const div = document.createElement("div");
            div.className = "warning-item";
            const title = document.createElement("div");
            title.style.fontWeight = "600";
            title.style.marginBottom = "0.15rem";
            title.textContent = `${item.title} (${phaseKeyToLabel(item.phaseKey)})`;
            div.appendChild(title);

            if (di.hardIssues.length) {
                const ul = document.createElement("ul");
                ul.style.margin = "0 0 0.15rem 1rem";
                ul.style.padding = "0";
                di.hardIssues.forEach(h => {
                    const li = document.createElement("li");
                    li.textContent = `Dipendenza obbligatoria: ${h.depTitle} (${phaseKeyToLabel(
                        h.depPhaseKey
                    )})`;
                    ul.appendChild(li);
                });
                div.appendChild(ul);
            }
            if (di.softIssues.length) {
                const ul = document.createElement("ul");
                ul.style.margin = "0 0 0.15rem 1rem";
                ul.style.padding = "0";
                di.softIssues.forEach(s => {
                    const li = document.createElement("li");
                    li.textContent = `Dipendenza consigliata: ${s.depTitle} (${phaseKeyToLabel(
                        s.depPhaseKey
                    )})`;
                    ul.appendChild(li);
                });
                div.appendChild(ul);
            }

            container.appendChild(div);
        });
    }

    // --- Esportazione Markdown ---

    function exportMarkdown() {
        const { summary, totalPoints, totalDays, totalCost } = computePhaseSummary();
        const { ratePerDay, hoursPerDay, hoursPerPoint, aiFactor } = state.params;

        const byPhaseKey = {
            phase1: "Fase 1",
            phase2: "Fase 2",
            phase3: "Fase 3",
            phase4: "Fase 4"
        };

        let md = "";
        md += "# Preventivo parametrico – Gestionale Altramusica\n\n";

        md += "## Parametri utilizzati\n\n";
        md += `- Tariffa giornaliera: **${ratePerDay} €**\n`;
        md += `- Ore per giorno: **${hoursPerDay} h**\n`;
        md += `- Ore per punto funzione: **${hoursPerPoint} h**\n`;
        md += `- Accelerazione IA su funzioni meccaniche: **${Math.round(
            aiFactor * 100
        )}%**\n\n`;

        md += "## Riepilogo per fase\n\n";
        md += "| Fase | Punti | Giorni stimati | Costo stimato (€) |\n";
        md += "|------|-------|----------------|--------------------|\n";
        ["phase1", "phase2", "phase3", "phase4"].forEach(pk => {
            const p = summary[pk];
            md += `| ${byPhaseKey[pk]} | ${p.points.toFixed(1)} | ${p.days.toFixed(
                1
            )} | ${Math.round(p.cost)} |\n`;
        });
        md += `| **Totale** | **${totalPoints.toFixed(1)}** | **${totalDays.toFixed(
            1
        )}** | **${Math.round(totalCost)}** |\n\n`;

        md += "## Dettaglio per fase\n\n";

        ["phase1", "phase2", "phase3", "phase4"].forEach(pk => {
            const label = byPhaseKey[pk];
            md += `### ${label}\n\n`;
            const items = state.items.filter(i => i.phaseKey === pk);
            if (!items.length) {
                md += "_Nessuna funzionalità assegnata._\n\n";
                return;
            }
            md += "| Funzionalità | Macro-area | Note | Dipendenze critiche |\n";
            md += "|--------------|-----------|------|----------------------|\n";
            items.forEach(item => {
                const di = getDependencyIssues(item);
                const crit = [];
                di.hardIssues.forEach(h => {
                    crit.push(h.depTitle);
                });
                const critStr = crit.length ? crit.join(", ") : "";
                const safeTitle = item.title.replace(/\|/g, "\\|");
                const safeMacro = (item.macroArea || "").replace(/\|/g, "\\|");
                const safeNotes = (item.notes || "").replace(/\|/g, "\\|");
                const safeCrit = critStr.replace(/\|/g, "\\|");
                md += `| ${safeTitle} | ${safeMacro} | ${safeNotes} | ${safeCrit} |\n`;
            });
            md += "\n";
        });

        copyMarkdownToClipboard(md);
    }

    function copyMarkdownToClipboard(md) {
        const output = document.getElementById("exportOutput");
        output.value = md;

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard
                .writeText(md)
                .then(() => {
                    alert("Markdown copiato negli appunti.");
                })
                .catch(() => {
                    // Fallback: seleziona il testo
                    output.focus();
                    output.select();
                });
        } else {
            output.focus();
            output.select();
        }
    }

    // Avvio
    document.addEventListener("DOMContentLoaded", init);
})();


