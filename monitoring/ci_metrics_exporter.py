#!/usr/bin/env python3
import json, os
from http.server import BaseHTTPRequestHandler, HTTPServer
PORT = int(os.environ.get("EXPORTER_PORT","9106"))
data = {"ci_build_duration_seconds":0.0, "ci_coverage_percent":0.0}

# optional inputs
data["ci_build_duration_seconds"] = float(os.environ.get("CI_BUILD_SECONDS","0"))
try:
    with open(os.environ.get("COVERAGE_JSON","metrics/coverage.json"), "r") as f:
        j = json.load(f)
        data["ci_coverage_percent"] = float(j.get("coverage",0))
except Exception:
    pass

class H(BaseHTTPRequestHandler):
    def do_GET(self):
        if self.path != "/metrics":
            self.send_response(404); self.end_headers(); return
        self.send_response(200); self.send_header("Content-Type","text/plain; version=0.0.4"); self.end_headers()
        self.wfile.write((
            "# HELP ci_build_duration_seconds Build duration in seconds\n"
            "# TYPE ci_build_duration_seconds gauge\n"
            f"ci_build_duration_seconds {data['ci_build_duration_seconds']}\n"
            "# HELP ci_coverage_percent Test coverage percent\n"
            "# TYPE ci_coverage_percent gauge\n"
            f"ci_coverage_percent {data['ci_coverage_percent']}\n"
        ).encode())

if __name__ == "__main__":
    srv = HTTPServer(("0.0.0.0", PORT), H)
    try:
        srv.serve_forever()
    except KeyboardInterrupt:
        pass
