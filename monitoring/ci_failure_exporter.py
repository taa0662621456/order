#!/usr/bin/env python3
import os, sys, time
from http.server import BaseHTTPRequestHandler, HTTPServer
from threading import Thread

FAIL_TYPE = (sys.argv[1] if len(sys.argv) > 1 else os.environ.get("CI_FAILURE_TYPE","unknown")).lower()
PORT = int(os.environ.get("EXPORTER_PORT","9105"))
metrics = {"ci_failures_total": {"tests":0,"deploy":0,"build":0,"migration":0,"unknown":0}}

def inc(ft):
    ft = ft if ft in metrics["ci_failures_total"] else "unknown"
    metrics["ci_failures_total"][ft] += 1

inc(FAIL_TYPE)

class H(BaseHTTPRequestHandler):
    def do_GET(self):
        if self.path != "/metrics":
            self.send_response(404); self.end_headers(); return
        self.send_response(200)
        self.send_header("Content-Type","text/plain; version=0.0.4"); self.end_headers()
        out = []
        out.append("# HELP ci_failures_total Number of CI failures per type")
        out.append("# TYPE ci_failures_total counter")
        for t,v in metrics["ci_failures_total"].items():
            out.append(f'ci_failures_total{{type="{t}"}} {v}')
        self.wfile.write(("\n".join(out)+"\n").encode())

if __name__ == "__main__":
    srv = HTTPServer(("0.0.0.0", PORT), H)
    try:
        srv.serve_forever()
    except KeyboardInterrupt:
        pass
