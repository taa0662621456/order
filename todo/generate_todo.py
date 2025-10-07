#!/usr/bin/env python3
import json, sys, pathlib

def make_todo(spec: dict) -> str:
    lines = ["# Order Component — TODO (α)\n"]
    prio_order = {"P0": 0, "P1": 1, "P2": 2, "P3": 3}
    blocks = sorted(spec.items(), key=lambda kv: prio_order.get(kv[1].get("priority","P2"), 9))
    for name, block in blocks:
        pr = block.get("priority","P2")
        lines.append(f"## {name} — {pr}\n")
        for it in block.get("items", []):
            lines.append(f"- [ ] {it}")
        lines.append("")
    lines.append("## Milestones\n")
    lines.extend([
        "- [ ] α-1 Domain Layer stabilized",
        "- [ ] α-2 Transactional App Layer",
        "- [ ] α-3 Pricing & Tax Engine",
        "- [ ] α-4 Payment & Shipment modules",
        "- [ ] α-5 QA / CI/CD / Docs (tag v0.1.0-alpha)",
        ""
    ])
    return "\n".join(lines)

def main():
    path = pathlib.Path(sys.argv[1]) if len(sys.argv) > 1 else pathlib.Path('todo_spec.json')
    spec = json.loads(path.read_text(encoding='utf-8'))
    out = make_todo(spec)
    pathlib.Path('todo.md').write_text(out, encoding='utf-8')
    print("Wrote todo.md")

if __name__ == "__main__":
    main()
