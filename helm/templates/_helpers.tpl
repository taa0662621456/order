{{- define "order-api-canary.name" -}}
order-api
{{- end -}}

{{- define "order-api-canary.fullname" -}}
{{ .Release.Name }}
{{- end -}}
