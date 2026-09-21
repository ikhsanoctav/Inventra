@extends('layouts.guest')
2: 
3: @section('title', 'INVENTRA — Sistem Manajemen Inventaris Terintegrasi')
4: 
5: @section('styles')
6: <script id="tailwind-config">
7:             tailwind.config = {
8:                 darkMode: "class",
9:                 theme: {
10:                     extend: {
11:                         colors: {
12:                             "error-container": "#fee2e2",
13:                             "surface-container-highest": "#e2e8f0",
14:                             "on-secondary-container": "#475569",
15:                             "on-error": "#ffffff",
16:                             "inverse-on-surface": "#f1f5f9",
17:                             "outline-variant": "#cbd5e1",
18:                             "on-primary-container": "#ffffff",
19:                             "on-error-container": "#7f1d1d",
20:                             "inverse-surface": "#1e293b",
21:                             "on-secondary-fixed-variant": "#334155",
22:                             "primary-container": "#2563eb",
23:                             "surface-tint": "#1d4ed8",
24:                             "inverse-primary": "#bfdbfe",
25:                             "secondary-fixed-dim": "#94a3b8",
26:                             outline: "#64748b",
27:                             "on-tertiary-fixed-variant": "#0c4a6e",
28:                             tertiary: "#0ea5e9",
29:                             "secondary-container": "#e2e8f0",
30:                             "on-background": "#0f172a",
31:                             "surface-container-high": "#f1f5f9",
32:                             "on-primary-fixed-variant": "#1e3a8a",
33:                             "on-surface": "#0f172a",
34:                             "surface-variant": "#f1f5f9",
35:                             secondary: "#64748b",
36:                             "surface-container-low": "#f8fafc",
37:                             background: "#f8fafc",
38:                             "on-primary-fixed": "#ffffff",
39:                             "secondary-fixed": "#e2e8f0",
40:                             "on-tertiary-container": "#ffffff",
41:                             "on-tertiary-fixed": "#082f49",
42:                             "tertiary-container": "#38bdf8",
43:                             "tertiary-fixed": "#e0f2fe",
44:                             "on-secondary": "#ffffff",
45:                             "on-secondary-fixed": "#0f172a",
46:                             "on-tertiary": "#ffffff",
47:                             "surface-container-lowest": "#ffffff",
48:                             surface: "#ffffff",
49:                             "on-surface-variant": "#475569",
50:                             error: "#ef4444",
51:                             "surface-container": "#f8fafc",
52:                             "on-primary": "#ffffff",
53:                             "primary-fixed-dim": "#93c5fd",
54:                             primary: "#1d4ed8",
55:                             "primary-fixed": "#dbeafe",
56:                             "surface-dim": "#e2e8f0",
57:                             "tertiary-fixed-dim": "#bae6fd",
58:                             "surface-bright": "#ffffff",
59:                         },
60:                         borderRadius: {
61:                             DEFAULT: "0.25rem",
62:                             lg: "0.5rem",
63:                             xl: "0.75rem",
64:                             full: "9999px",
65:                         },
66:                         spacing: {
67:                             "space-sm": "0.5rem",
68:                             "space-lg": "1.25rem",
69:                             "sidebar-width": "16rem",
70:                             "sidebar-collapsed": "4.5rem",
71:                             "space-2xl": "2rem",
72:                             "space-xs": "0.25rem",
73:                             "space-3xl": "2.5rem",
74:                             "space-xl": "1.5rem",
75:                             "gutter-mobile": "1rem",
76:                             "gutter-desktop": "1.5rem",
77:                             "space-base": "1rem",
78:                             "space-2xs": "0.125rem",
79:                             "space-md": "0.75rem",
80:                         },
81:                         fontFamily: {
82:                             "headline-lg": ["Plus Jakarta Sans"],
83:                             display: ["Plus Jakarta Sans"],
84:                             "body-md": ["Inter"],
85:                             "label-sm": ["Inter"],
86:                             "body-lg": ["Inter"],
87:                             "label-md": ["Inter"],
88:                             "headline-md": ["Plus Jakarta Sans"],
89:                             "headline-sm": ["Plus Jakarta Sans"],
90:                             "tabular-number": ["Inter"],
91:                             "body-sm": ["Inter"],
92:                             "display-mobile": ["Plus Jakarta Sans"],
93:                         },
94:                         fontSize: {
95:                             "headline-lg": [
96:                                 "24px",
97:                                 {
98:                                     lineHeight: "32px",
99:                                     letterSpacing: "-0.02em",
100:                                     fontWeight: "600",
101:                                 },
102:                             ],
103:                             display: [
104:                                 "36px",
105:                                 {
106:                                     lineHeight: "44px",
107:                                     letterSpacing: "-0.025em",
108:                                     fontWeight: "700",
109:                                 },
110:                             ],
111:                             "body-md": [
112:                                 "14px",
113:                                 {
114:                                     lineHeight: "20px",
115:                                     letterSpacing: "0em",
116:                                     fontWeight: "400",
117:                                 },
118:                             ],
119:                             "label-sm": [
120:                                 "11px",
121:                                 {
122:                                     lineHeight: "14px",
123:                                     letterSpacing: "0.03em",
124:                                     fontWeight: "600",
125:                                 },
126:                             ],
127:                             "body-lg": [
128:                                 "15px",
129:                                 {
130:                                     lineHeight: "24px",
131:                                     letterSpacing: "-0.005em",
132:                                     fontWeight: "400",
133:                                 },
134:                             ],
135:                             "label-md": [
136:                                 "12px",
137:                                 {
138:                                     lineHeight: "16px",
139:                                     letterSpacing: "0.01em",
140:                                     fontWeight: "500",
141:                                 },
142:                             ],
143:                             "headline-md": [
144:                                 "20px",
145:                                 {
146:                                     lineHeight: "28px",
147:                                     letterSpacing: "-0.015em",
148:                                     fontWeight: "600",
149:                                 },
150:                             ],
151:                             "headline-sm": [
152:                                 "16px",
153:                                 {
154:                                     lineHeight: "24px",
155:                                     letterSpacing: "-0.01em",
156:                                     fontWeight: "600",
157:                                 },
158:                             ],
159:                             "tabular-number": [
160:                                 "14px",
161:                                 {
162:                                     lineHeight: "20px",
163:                                     letterSpacing: "-0.01em",
164:                                     fontWeight: "500",
165:                                 },
166:                             ],
167:                             "body-sm": [
168:                                 "13px",
169:                                 {
170:                                     lineHeight: "18px",
171:                                     letterSpacing: "0em",
172:                                     fontWeight: "400",
173:                                 },
174:                             ],
175:                             "display-mobile": [
176:                                 "28px",
177:                                 {
178:                                     lineHeight: "36px",
179:                                     letterSpacing: "-0.02em",
180:                                     fontWeight: "700",
181:                                 },
182:                             ],
183:                         },
184:                     },
185:                 },
186:             };
187:         </script>
188: <style>
189:             .material-symbols-outlined {
190:                 font-variation-settings:
191:                     "FILL" 0,
192:                     "wght" 400,
193:                     "GRAD" 0,
194:                     "opsz" 24;
195:                 display: inline-block;
196:                 vertical-align: middle;
197:                 line-height: 1;
198:             }
199:             .custom-shadow-soft {
200:                 box-shadow:
201:                     0 1px 3px 0 rgba(17, 24, 39, 0.04),
202:                     0 1px 2px -1px rgba(17, 24, 39, 0.03);
203:             }
204:             .custom-shadow-elevated {
205:                 box-shadow:
206:                     0 12px 24px -4px rgba(17, 24, 39, 0.06),
207:                     0 4px 8px -2px rgba(17, 24, 39, 0.03);
208:             }
209:         </style>
210: @endsection