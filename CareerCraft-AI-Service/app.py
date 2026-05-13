"""
CareerCraft AI Service — Enhanced Dataset
==========================================
Dataset: 110 skills · 20 job roles · 38 course catalogs · salary benchmarks
Sources: skill2vec (GitHub), LinkedIn Job Data 2024, O*NET, industry standards
"""

from flask import Flask, request, jsonify
import numpy as np
from typing import Dict, List
import random

app = Flask(__name__)

# ══════════════════════════════════════════════════════════
# SKILL CATALOG — 110 Skills across 12 Categories
# ══════════════════════════════════════════════════════════

SKILL_CATALOG_FULL: Dict[str, Dict] = {
    "Python":        {"category": "Programming", "demand": 0.95},
    "JavaScript":    {"category": "Programming", "demand": 0.93},
    "Java":          {"category": "Programming", "demand": 0.88},
    "TypeScript":    {"category": "Programming", "demand": 0.87},
    "C++":           {"category": "Programming", "demand": 0.80},
    "C#":            {"category": "Programming", "demand": 0.78},
    "Go":            {"category": "Programming", "demand": 0.82},
    "Rust":          {"category": "Programming", "demand": 0.70},
    "PHP":           {"category": "Programming", "demand": 0.72},
    "Swift":         {"category": "Programming", "demand": 0.75},
    "Kotlin":        {"category": "Programming", "demand": 0.74},
    "R":             {"category": "Programming", "demand": 0.76},
    "Scala":         {"category": "Programming", "demand": 0.68},
    "Ruby":          {"category": "Programming", "demand": 0.65},
    "MATLAB":        {"category": "Programming", "demand": 0.62},
    "Solidity":      {"category": "Programming", "demand": 0.72},
    "React":         {"category": "Frontend", "demand": 0.92},
    "Vue.js":        {"category": "Frontend", "demand": 0.82},
    "Angular":       {"category": "Frontend", "demand": 0.80},
    "Next.js":       {"category": "Frontend", "demand": 0.85},
    "HTML/CSS":      {"category": "Frontend", "demand": 0.90},
    "Tailwind CSS":  {"category": "Frontend", "demand": 0.83},
    "Redux":         {"category": "Frontend", "demand": 0.75},
    "GraphQL":       {"category": "Frontend", "demand": 0.78},
    "Three.js":      {"category": "Frontend", "demand": 0.60},
    "Node.js":       {"category": "Backend", "demand": 0.88},
    "Django":        {"category": "Backend", "demand": 0.80},
    "FastAPI":       {"category": "Backend", "demand": 0.82},
    "Flask":         {"category": "Backend", "demand": 0.78},
    "Spring Boot":   {"category": "Backend", "demand": 0.82},
    "REST APIs":     {"category": "Backend", "demand": 0.92},
    "Microservices": {"category": "Backend", "demand": 0.85},
    "gRPC":          {"category": "Backend", "demand": 0.72},
    "Express.js":    {"category": "Backend", "demand": 0.80},
    "Laravel":       {"category": "Backend", "demand": 0.70},
    "SQL":           {"category": "Database", "demand": 0.93},
    "PostgreSQL":    {"category": "Database", "demand": 0.87},
    "MySQL":         {"category": "Database", "demand": 0.85},
    "MongoDB":       {"category": "Database", "demand": 0.83},
    "Redis":         {"category": "Database", "demand": 0.80},
    "Elasticsearch": {"category": "Database", "demand": 0.75},
    "Cassandra":     {"category": "Database", "demand": 0.65},
    "DynamoDB":      {"category": "Database", "demand": 0.72},
    "AWS":              {"category": "Cloud", "demand": 0.93},
    "Azure":            {"category": "Cloud", "demand": 0.88},
    "GCP":              {"category": "Cloud", "demand": 0.85},
    "Docker":           {"category": "DevOps", "demand": 0.90},
    "Kubernetes":       {"category": "DevOps", "demand": 0.87},
    "Terraform":        {"category": "DevOps", "demand": 0.82},
    "CI/CD":            {"category": "DevOps", "demand": 0.88},
    "Git":              {"category": "DevOps", "demand": 0.95},
    "Linux":            {"category": "DevOps", "demand": 0.88},
    "Jenkins":          {"category": "DevOps", "demand": 0.75},
    "GitHub Actions":   {"category": "DevOps", "demand": 0.82},
    "Ansible":          {"category": "DevOps", "demand": 0.72},
    "Prometheus":       {"category": "DevOps", "demand": 0.70},
    "Grafana":          {"category": "DevOps", "demand": 0.70},
    "Machine Learning":   {"category": "AI/ML", "demand": 0.92},
    "Deep Learning":      {"category": "AI/ML", "demand": 0.88},
    "TensorFlow":         {"category": "AI/ML", "demand": 0.83},
    "PyTorch":            {"category": "AI/ML", "demand": 0.85},
    "Scikit-learn":       {"category": "AI/ML", "demand": 0.84},
    "NLP":                {"category": "AI/ML", "demand": 0.82},
    "Computer Vision":    {"category": "AI/ML", "demand": 0.80},
    "LLMs":               {"category": "AI/ML", "demand": 0.88},
    "MLOps":              {"category": "AI/ML", "demand": 0.82},
    "Data Analysis":      {"category": "Data", "demand": 0.90},
    "Data Visualization": {"category": "Data", "demand": 0.85},
    "Statistics":         {"category": "Data", "demand": 0.85},
    "Pandas":             {"category": "Data", "demand": 0.87},
    "NumPy":              {"category": "Data", "demand": 0.85},
    "Power BI":           {"category": "Data", "demand": 0.80},
    "Tableau":            {"category": "Data", "demand": 0.78},
    "Apache Spark":       {"category": "Data", "demand": 0.78},
    "dbt":                {"category": "Data", "demand": 0.72},
    "Airflow":            {"category": "Data", "demand": 0.75},
    "Cybersecurity":       {"category": "Security", "demand": 0.90},
    "Penetration Testing": {"category": "Security", "demand": 0.82},
    "SIEM":                {"category": "Security", "demand": 0.75},
    "Ethical Hacking":     {"category": "Security", "demand": 0.78},
    "Network Security":    {"category": "Security", "demand": 0.80},
    "Zero Trust":          {"category": "Security", "demand": 0.72},
    "OWASP":               {"category": "Security", "demand": 0.75},
    "Cryptography":        {"category": "Security", "demand": 0.70},
    "SOC":                 {"category": "Security", "demand": 0.75},
    "Incident Response":   {"category": "Security", "demand": 0.78},
    "React Native":        {"category": "Mobile", "demand": 0.82},
    "Flutter":             {"category": "Mobile", "demand": 0.80},
    "iOS Development":     {"category": "Mobile", "demand": 0.78},
    "Android Development": {"category": "Mobile", "demand": 0.80},
    "UI/UX Design":  {"category": "Design", "demand": 0.85},
    "Figma":         {"category": "Design", "demand": 0.82},
    "Adobe XD":      {"category": "Design", "demand": 0.70},
    "User Research": {"category": "Design", "demand": 0.78},
    "Prototyping":   {"category": "Design", "demand": 0.75},
    "Ethereum":        {"category": "Blockchain", "demand": 0.72},
    "Smart Contracts": {"category": "Blockchain", "demand": 0.70},
    "Web3.js":         {"category": "Blockchain", "demand": 0.68},
    "Project Management": {"category": "Management", "demand": 0.85},
    "Agile":              {"category": "Management", "demand": 0.88},
    "Scrum":              {"category": "Management", "demand": 0.85},
    "Product Management": {"category": "Management", "demand": 0.82},
    "Communication":      {"category": "Soft Skills", "demand": 0.95},
    "Problem Solving":    {"category": "Soft Skills", "demand": 0.95},
    "Teamwork":           {"category": "Soft Skills", "demand": 0.92},
    "Critical Thinking":  {"category": "Soft Skills", "demand": 0.90},
    "Time Management":    {"category": "Soft Skills", "demand": 0.88},
    "Leadership":         {"category": "Soft Skills", "demand": 0.82},
    "Analytical Thinking":{"category": "Soft Skills", "demand": 0.90},
}

SKILL_CATALOG: List[str] = list(SKILL_CATALOG_FULL.keys())
N = len(SKILL_CATALOG)
SKILL_INDEX: Dict[str, int] = {skill: i for i, skill in enumerate(SKILL_CATALOG)}

# ══════════════════════════════════════════════════════════
# JOB ROLES — 20 Roles
# ══════════════════════════════════════════════════════════

JOB_ROLES: Dict[str, Dict] = {
    "Data Scientist": {
        "required_skills": ["Python","Machine Learning","Deep Learning","Statistics","SQL",
                            "Data Analysis","Data Visualization","Pandas","NumPy","Scikit-learn",
                            "Git","Communication","Problem Solving","Analytical Thinking"],
        "critical_skills": ["Python","Machine Learning","Statistics","Scikit-learn"],
        "nice_to_have":    ["TensorFlow","PyTorch","Apache Spark","Tableau","R"],
        "market_demand": 0.95, "avg_salary_usd": 130000,
        "experience_levels": ["Mid","Senior"],
        "description": "Analyzes complex data to extract insights and build predictive models.",
        "interview_questions": [
            "Tell me about a machine learning project you've built end-to-end.",
            "How do you handle imbalanced datasets?",
            "Explain the bias-variance tradeoff.",
            "Walk me through how you'd approach a new data problem from scratch.",
            "How do you evaluate and select the best model?"
        ]
    },
    "ML Engineer": {
        "required_skills": ["Python","Machine Learning","Deep Learning","TensorFlow","PyTorch",
                            "MLOps","Docker","Kubernetes","REST APIs","Git","Linux","Statistics","Problem Solving"],
        "critical_skills": ["Python","Machine Learning","Deep Learning","MLOps","Docker"],
        "nice_to_have":    ["Apache Spark","Airflow","AWS","GCP"],
        "market_demand": 0.93, "avg_salary_usd": 155000,
        "experience_levels": ["Mid","Senior"],
        "description": "Builds and deploys ML models at scale in production environments.",
        "interview_questions": [
            "How do you deploy a machine learning model to production?",
            "What is model drift and how do you monitor for it?",
            "Explain the difference between batch and online inference.",
            "How do you version control ML models?",
            "Describe your MLOps pipeline experience."
        ]
    },
    "Data Engineer": {
        "required_skills": ["Python","SQL","Apache Spark","Airflow","dbt","AWS",
                            "Docker","PostgreSQL","MongoDB","Git","Linux","Problem Solving","Analytical Thinking"],
        "critical_skills": ["Python","SQL","Apache Spark","Airflow","AWS"],
        "nice_to_have":    ["GCP","Terraform","Kubernetes"],
        "market_demand": 0.91, "avg_salary_usd": 135000,
        "experience_levels": ["Mid","Senior"],
        "description": "Designs and maintains data pipelines and infrastructure.",
        "interview_questions": [
            "Design a data pipeline for processing 10TB of daily logs.",
            "What is the difference between ETL and ELT?",
            "How do you ensure data quality in a pipeline?",
            "Explain partitioning in Apache Spark.",
            "How do you handle late-arriving data?"
        ]
    },
    "Data Analyst": {
        "required_skills": ["SQL","Data Analysis","Data Visualization","Statistics",
                            "Power BI","Tableau","Python","Communication","Critical Thinking",
                            "Analytical Thinking","Problem Solving"],
        "critical_skills": ["SQL","Data Analysis","Data Visualization","Statistics"],
        "nice_to_have":    ["R","Pandas","dbt"],
        "market_demand": 0.88, "avg_salary_usd": 85000,
        "experience_levels": ["Junior","Mid","Senior"],
        "description": "Transforms raw data into actionable business insights.",
        "interview_questions": [
            "Walk me through your approach to a new analytical problem.",
            "How do you ensure data quality before analysis?",
            "Describe the most impactful dashboard you built.",
            "Write a SQL query to find the top 5 customers by revenue.",
            "How do you communicate findings to non-technical stakeholders?"
        ]
    },
    "Backend Developer": {
        "required_skills": ["Python","Node.js","SQL","REST APIs","Docker",
                            "Git","Linux","PostgreSQL","Redis","Microservices","Problem Solving","Teamwork"],
        "critical_skills": ["Python","REST APIs","Docker","SQL","Microservices"],
        "nice_to_have":    ["Kubernetes","Go","Elasticsearch","AWS"],
        "market_demand": 0.91, "avg_salary_usd": 125000,
        "experience_levels": ["Junior","Mid","Senior"],
        "description": "Builds scalable server-side applications and APIs.",
        "interview_questions": [
            "Design a rate-limiting system for an API.",
            "How do you handle database transactions?",
            "Explain the CAP theorem.",
            "How do you secure a REST API?",
            "Describe a performance bottleneck you found and fixed."
        ]
    },
    "Frontend Developer": {
        "required_skills": ["JavaScript","TypeScript","React","HTML/CSS","Next.js",
                            "Redux","REST APIs","Git","Tailwind CSS","Problem Solving","Communication","Teamwork"],
        "critical_skills": ["JavaScript","React","TypeScript","HTML/CSS"],
        "nice_to_have":    ["Vue.js","GraphQL","Three.js"],
        "market_demand": 0.89, "avg_salary_usd": 115000,
        "experience_levels": ["Junior","Mid","Senior"],
        "description": "Creates responsive and interactive user interfaces.",
        "interview_questions": [
            "Explain the virtual DOM and React reconciliation.",
            "How do you optimize a React app's performance?",
            "What is the difference between SSR and CSR?",
            "Describe how you manage state in a large React app.",
            "How do you approach accessibility in web apps?"
        ]
    },
    "Full Stack Developer": {
        "required_skills": ["JavaScript","TypeScript","React","Node.js","SQL",
                            "REST APIs","Docker","Git","HTML/CSS","MongoDB","Problem Solving","Communication"],
        "critical_skills": ["JavaScript","React","Node.js","SQL","REST APIs"],
        "nice_to_have":    ["Next.js","GraphQL","AWS","Kubernetes"],
        "market_demand": 0.90, "avg_salary_usd": 120000,
        "experience_levels": ["Junior","Mid","Senior"],
        "description": "Works across the full stack from database to UI.",
        "interview_questions": [
            "Walk me through how a web request flows in your app.",
            "How do you handle authentication in a full-stack app?",
            "Describe your deployment process.",
            "How do you handle CORS?",
            "Explain database indexing and when to use it."
        ]
    },
    "DevOps Engineer": {
        "required_skills": ["Docker","Kubernetes","AWS","Terraform","CI/CD",
                            "Linux","Git","Python","Ansible","Prometheus","Grafana","Problem Solving","Communication"],
        "critical_skills": ["Docker","Kubernetes","AWS","CI/CD","Terraform"],
        "nice_to_have":    ["Azure","GCP","Vault","ArgoCD"],
        "market_demand": 0.92, "avg_salary_usd": 135000,
        "experience_levels": ["Mid","Senior"],
        "description": "Automates infrastructure and deployment pipelines.",
        "interview_questions": [
            "Explain blue-green vs canary deployments.",
            "How do you design a CI/CD pipeline from scratch?",
            "How do you handle secrets management in Kubernetes?",
            "What is Infrastructure as Code and why does it matter?",
            "How do you troubleshoot a failing pod in Kubernetes?"
        ]
    },
    "Cloud Architect": {
        "required_skills": ["AWS","Azure","GCP","Terraform","Kubernetes","Docker",
                            "Microservices","Network Security","CI/CD","Linux",
                            "Problem Solving","Communication","Leadership"],
        "critical_skills": ["AWS","Terraform","Kubernetes","Microservices"],
        "nice_to_have":    ["CDK","Pulumi"],
        "market_demand": 0.90, "avg_salary_usd": 165000,
        "experience_levels": ["Senior"],
        "description": "Designs scalable, secure cloud solutions.",
        "interview_questions": [
            "How do you design for high availability across regions?",
            "Walk me through a cloud migration you led.",
            "How do you optimize cloud costs?",
            "Explain the shared responsibility model.",
            "How do you handle disaster recovery in the cloud?"
        ]
    },
    "Cybersecurity Analyst": {
        "required_skills": ["Cybersecurity","Network Security","SIEM","Incident Response",
                            "OWASP","Python","Linux","Ethical Hacking","SQL",
                            "Critical Thinking","Problem Solving","Analytical Thinking"],
        "critical_skills": ["Cybersecurity","SIEM","Incident Response","Network Security"],
        "nice_to_have":    ["Penetration Testing","Zero Trust","SOC","Cryptography"],
        "market_demand": 0.93, "avg_salary_usd": 120000,
        "experience_levels": ["Junior","Mid","Senior"],
        "description": "Protects systems and data from cyber threats.",
        "interview_questions": [
            "Walk me through your incident response process.",
            "What is a SQL injection attack and how do you prevent it?",
            "Explain the difference between IDS and IPS.",
            "How do you perform a vulnerability assessment?",
            "Describe a security incident you handled."
        ]
    },
    "Penetration Tester": {
        "required_skills": ["Penetration Testing","Ethical Hacking","OWASP","Python",
                            "Network Security","Linux","Cybersecurity","Cryptography",
                            "Problem Solving","Critical Thinking"],
        "critical_skills": ["Penetration Testing","Ethical Hacking","OWASP"],
        "nice_to_have":    ["Zero Trust","SOC"],
        "market_demand": 0.88, "avg_salary_usd": 115000,
        "experience_levels": ["Mid","Senior"],
        "description": "Simulates attacks to find and fix security vulnerabilities.",
        "interview_questions": [
            "Describe your methodology for a web application pentest.",
            "How do you escalate privileges on a Linux system?",
            "What tools do you use for network scanning?",
            "Explain XSS and how to exploit it.",
            "How do you document findings in a pentest report?"
        ]
    },
    "Mobile Developer": {
        "required_skills": ["React Native","Flutter","JavaScript","TypeScript",
                            "iOS Development","Android Development","REST APIs","Git",
                            "Problem Solving","Teamwork"],
        "critical_skills": ["React Native","Flutter","REST APIs"],
        "nice_to_have":    ["Swift","Kotlin","Redux"],
        "market_demand": 0.85, "avg_salary_usd": 115000,
        "experience_levels": ["Junior","Mid","Senior"],
        "description": "Develops cross-platform or native mobile applications.",
        "interview_questions": [
            "Explain the difference between React Native and Flutter.",
            "How do you handle offline functionality in a mobile app?",
            "How do you optimize app startup time?",
            "Describe a complex UI component you built.",
            "How do you handle push notifications?"
        ]
    },
    "UI/UX Designer": {
        "required_skills": ["UI/UX Design","Figma","User Research","Prototyping",
                            "Adobe XD","Communication","Critical Thinking",
                            "Problem Solving","Teamwork","Analytical Thinking"],
        "critical_skills": ["UI/UX Design","Figma","User Research","Prototyping"],
        "nice_to_have":    ["HTML/CSS","React"],
        "market_demand": 0.85, "avg_salary_usd": 100000,
        "experience_levels": ["Junior","Mid","Senior"],
        "description": "Designs intuitive and beautiful user experiences.",
        "interview_questions": [
            "Walk me through your design process for a new feature.",
            "How do you conduct user research?",
            "Describe a design decision you made based on data.",
            "How do you handle conflicting feedback from stakeholders?",
            "Show me a design you're most proud of and explain why."
        ]
    },
    "Product Manager": {
        "required_skills": ["Product Management","Agile","Scrum","Communication",
                            "Data Analysis","SQL","Problem Solving","Leadership",
                            "Critical Thinking","Teamwork","Analytical Thinking"],
        "critical_skills": ["Product Management","Agile","Communication","Data Analysis"],
        "nice_to_have":    ["Power BI","Figma"],
        "market_demand": 0.88, "avg_salary_usd": 145000,
        "experience_levels": ["Mid","Senior"],
        "description": "Defines product vision and drives cross-functional teams.",
        "interview_questions": [
            "How do you prioritize features in your roadmap?",
            "Tell me about a product you took from idea to launch.",
            "How do you measure product success?",
            "How do you work with engineering when they push back on a timeline?",
            "Describe a failed product decision and what you learned."
        ]
    },
    "Software Engineer": {
        "required_skills": ["Python","Java","SQL","REST APIs","Git",
                            "Docker","Problem Solving","Teamwork","Communication","Agile","Scrum"],
        "critical_skills": ["Python","SQL","REST APIs","Git","Problem Solving"],
        "nice_to_have":    ["TypeScript","Kubernetes","AWS","Microservices"],
        "market_demand": 0.92, "avg_salary_usd": 130000,
        "experience_levels": ["Junior","Mid","Senior"],
        "description": "General software engineering across systems and products.",
        "interview_questions": [
            "Explain object-oriented programming principles.",
            "How do you approach debugging a complex issue?",
            "Describe your experience with code reviews.",
            "How do you write clean, maintainable code?",
            "Walk me through your approach to system design."
        ]
    },
    "NLP Engineer": {
        "required_skills": ["Python","NLP","Machine Learning","Deep Learning","LLMs",
                            "TensorFlow","PyTorch","Pandas","Git","Problem Solving","Analytical Thinking"],
        "critical_skills": ["Python","NLP","LLMs","PyTorch","Deep Learning"],
        "nice_to_have":    ["MLOps","AWS"],
        "market_demand": 0.90, "avg_salary_usd": 150000,
        "experience_levels": ["Mid","Senior"],
        "description": "Builds language models and NLP-powered applications.",
        "interview_questions": [
            "Explain attention mechanisms in transformers.",
            "How do you fine-tune a large language model?",
            "What is RAG and when would you use it?",
            "How do you evaluate an NLP model?",
            "Describe a production NLP system you built."
        ]
    },
    "Computer Vision Engineer": {
        "required_skills": ["Python","Computer Vision","Deep Learning","TensorFlow","PyTorch",
                            "Machine Learning","NumPy","Git","Problem Solving"],
        "critical_skills": ["Python","Computer Vision","Deep Learning","PyTorch"],
        "nice_to_have":    ["C++","MLOps"],
        "market_demand": 0.87, "avg_salary_usd": 148000,
        "experience_levels": ["Mid","Senior"],
        "description": "Builds vision-based AI systems for image and video understanding.",
        "interview_questions": [
            "Explain how CNNs work.",
            "How do you handle data augmentation for image classification?",
            "What is transfer learning and when do you use it?",
            "How do you deploy a vision model to edge devices?",
            "Describe a computer vision project you built."
        ]
    },
    "Database Administrator": {
        "required_skills": ["SQL","PostgreSQL","MySQL","MongoDB","Redis",
                            "Linux","Problem Solving","Analytical Thinking"],
        "critical_skills": ["SQL","PostgreSQL","MySQL"],
        "nice_to_have":    ["Elasticsearch","Cassandra","DynamoDB","AWS"],
        "market_demand": 0.80, "avg_salary_usd": 100000,
        "experience_levels": ["Mid","Senior"],
        "description": "Manages, optimizes, and secures database systems.",
        "interview_questions": [
            "How do you optimize a slow SQL query?",
            "Explain different types of database indexes.",
            "How do you plan database backup and recovery?",
            "What is database sharding?",
            "How do you handle database migrations with zero downtime?"
        ]
    },
    "Blockchain Developer": {
        "required_skills": ["Solidity","Ethereum","JavaScript","Python","Web3.js",
                            "Smart Contracts","Cryptography","REST APIs","Git","Problem Solving"],
        "critical_skills": ["Solidity","Smart Contracts","Ethereum","Web3.js"],
        "nice_to_have":    ["Rust","Zero Trust"],
        "market_demand": 0.75, "avg_salary_usd": 140000,
        "experience_levels": ["Mid","Senior"],
        "description": "Builds decentralized applications and smart contracts.",
        "interview_questions": [
            "Explain how Ethereum gas fees work.",
            "What are common smart contract vulnerabilities?",
            "How do you test a smart contract?",
            "Explain the difference between PoW and PoS.",
            "Describe a DApp you built."
        ]
    },
    "AI Engineer": {
        "required_skills": ["Python","Machine Learning","LLMs","REST APIs","Docker",
                            "MLOps","AWS","PyTorch","Git","Problem Solving","Communication","Analytical Thinking"],
        "critical_skills": ["Python","LLMs","MLOps","Machine Learning"],
        "nice_to_have":    ["Kubernetes","NLP","Computer Vision"],
        "market_demand": 0.95, "avg_salary_usd": 160000,
        "experience_levels": ["Mid","Senior"],
        "description": "Builds AI-powered products using LLMs and modern AI tooling.",
        "interview_questions": [
            "What is RAG and how do you implement it?",
            "How do you evaluate an LLM-powered application?",
            "Explain prompt engineering techniques.",
            "How do you handle hallucinations in LLM outputs?",
            "Describe an AI product you built end-to-end."
        ]
    },
}

# ══════════════════════════════════════════════════════════
# COURSE CATALOG
# ══════════════════════════════════════════════════════════

COURSE_CATALOG: Dict[str, List[str]] = {
    "Python":             ["Python Bootcamp (Udemy — José Portilla)", "CS50P (edX — Harvard free)", "Real Python (realpython.com)"],
    "Machine Learning":   ["ML Specialization (Coursera — Andrew Ng)", "Hands-on ML (O'Reilly)", "Fast.ai Practical ML (free)"],
    "Deep Learning":      ["Deep Learning Specialization (Coursera)", "Fast.ai (free)", "MIT 6.S191 (free)"],
    "TensorFlow":         ["TensorFlow Developer Certificate (Coursera)", "TensorFlow Official Tutorials (free)"],
    "PyTorch":            ["PyTorch for Deep Learning (Zero to Mastery)", "Fast.ai (free)"],
    "SQL":                ["SQL for Data Science (Coursera)", "Mode SQL Tutorial (free)", "SQLZoo (free)"],
    "Data Analysis":      ["Data Analysis with Python (freeCodeCamp free)", "DataCamp Data Analyst Track"],
    "Data Visualization": ["Storytelling with Data (book)", "Tableau (Coursera)", "D3.js Observable (free)"],
    "Statistics":         ["Statistics with Python (Coursera)", "Khan Academy Statistics (free)", "StatQuest YouTube (free)"],
    "JavaScript":         ["The Complete JavaScript Course (Udemy — Jonas)", "javascript.info (free)", "freeCodeCamp JS (free)"],
    "TypeScript":         ["Understanding TypeScript (Udemy)", "TypeScript Official Docs (free)"],
    "React":              ["React - The Complete Guide (Udemy)", "React Official Docs (free)", "Epic React (Kent C. Dodds)"],
    "Next.js":            ["Next.js & React (Udemy)", "Next.js Official Docs (free)"],
    "Node.js":            ["Node.js - The Complete Guide (Udemy)", "The Odin Project Node.js (free)"],
    "Docker":             ["Docker Mastery (Udemy — Bret Fisher)", "Docker Official Docs (free)"],
    "Kubernetes":         ["Kubernetes Mastery (Udemy)", "CKA Certification (Linux Foundation)"],
    "AWS":                ["AWS Certified Solutions Architect (Udemy — Cantrill)", "AWS Skill Builder (free)"],
    "Terraform":          ["Terraform on AWS (Udemy)", "HashiCorp Learn (free)"],
    "CI/CD":              ["DevOps with GitHub Actions (Udemy)", "Jenkins Fundamentals (free)"],
    "Git":                ["Git & GitHub Bootcamp (Udemy)", "Pro Git Book (free — git-scm.com)"],
    "Linux":              ["Linux Command Line (Udemy)", "Linux Journey (free — linuxjourney.com)"],
    "NLP":                ["NLP Specialization (Coursera — DeepLearning.AI)", "HuggingFace NLP Course (free)"],
    "LLMs":               ["LLM Engineering (Udemy)", "HuggingFace Course (free)", "DeepLearning.AI Short Courses (free)"],
    "Computer Vision":    ["Computer Vision Specialization (Coursera)", "OpenCV Python (Udemy)"],
    "MLOps":              ["MLOps Specialization (Coursera)", "Made With ML MLOps (free)"],
    "Cybersecurity":      ["CompTIA Security+ (Udemy)", "Google Cybersecurity Certificate (Coursera)"],
    "Penetration Testing":["Practical Ethical Hacking (TCM Security)", "OSCP Certification (Offensive Security)"],
    "UI/UX Design":       ["Google UX Design Certificate (Coursera)", "UX Design Bootcamp (Udemy)"],
    "Figma":              ["Figma UI UX Design (Udemy)", "Figma Learn (free)"],
    "React Native":       ["React Native - The Practical Guide (Udemy)", "React Native Docs (free)"],
    "Flutter":            ["Flutter & Dart (Udemy — Maximilian)", "Flutter Official Docs (free)"],
    "Apache Spark":       ["Taming Big Data with Apache Spark (Udemy)", "Databricks Academy (free)"],
    "Airflow":            ["Apache Airflow (Udemy)", "Astronomer Academy (free)"],
    "Power BI":           ["Power BI Desktop (Udemy)", "Microsoft Learn Power BI (free)"],
    "Tableau":            ["Tableau 2024 A-Z (Udemy)", "Tableau Public (free)"],
    "Agile":              ["PMI-ACP Agile (Udemy)", "Scrum.org Open Assessments (free)"],
    "Communication":      ["Business Communication (Coursera)", "Technical Writing (Google — free)"],
    "Problem Solving":    ["Computational Thinking (Coursera)", "LeetCode (free + premium)"],
}

# ══════════════════════════════════════════════════════════
# SALARY BENCHMARKS
# ══════════════════════════════════════════════════════════

SALARY_BENCHMARKS = {
    "USA":    {"junior": 0.65, "mid": 1.0,  "senior": 1.40},
    "Europe": {"junior": 0.50, "mid": 0.75, "senior": 1.05},
    "MENA":   {"junior": 0.25, "mid": 0.40, "senior": 0.60},
    "Remote": {"junior": 0.55, "mid": 0.80, "senior": 1.15},
}

# ══════════════════════════════════════════════════════════
# HELPER FUNCTIONS
# ══════════════════════════════════════════════════════════

def parse_skills(raw) -> List[str]:
    if isinstance(raw, list):
        return [str(s).strip() for s in raw if s]
    if isinstance(raw, str):
        return [s.strip() for s in raw.split(",") if s.strip()]
    return []

def get_experience_weight(years: int) -> float:
    if years <= 2:   return 0.7
    elif years <= 5: return 0.85
    return 1.0

def build_weighted_vector(skills, critical_skills, experience_years):
    exp_weight = get_experience_weight(experience_years)
    vector = np.zeros(N)
    for skill in skills:
        if skill in SKILL_INDEX:
            vector[SKILL_INDEX[skill]] = (2.0 if skill in critical_skills else 1.0) * exp_weight
    return vector

def cosine_similarity(a, b):
    na, nb = np.linalg.norm(a), np.linalg.norm(b)
    if na == 0 or nb == 0: return 0.0
    return float(np.dot(a, b) / (na * nb))

def jaccard_similarity(set_a, set_b):
    union = len(set_a | set_b)
    return 0.0 if union == 0 else len(set_a & set_b) / union

COSINE_WEIGHT  = 0.50
JACCARD_WEIGHT = 0.30
DEMAND_WEIGHT  = 0.20
INTEREST_BONUS = 0.10

def compute_score(user_skills, experience_years, job_name, job_data, career_interests):
    user_vec = build_weighted_vector(user_skills, job_data["critical_skills"], experience_years)
    job_vec  = build_weighted_vector(job_data["required_skills"], job_data["critical_skills"], experience_years)
    cos   = cosine_similarity(user_vec, job_vec)
    jac   = jaccard_similarity(set(user_skills), set(job_data["required_skills"]))
    bonus = INTEREST_BONUS if job_name in career_interests else 0.0
    score = COSINE_WEIGHT*cos + JACCARD_WEIGHT*jac + DEMAND_WEIGHT*job_data["market_demand"] + bonus
    if score >= 0.75:   match_level = "Excellent Match"
    elif score >= 0.55: match_level = "Good Match"
    elif score >= 0.35: match_level = "Partial Match"
    else:               match_level = "Low Match"
    return {
        "title": job_name, "cosine_similarity": round(cos, 4),
        "jaccard_similarity": round(jac, 4), "market_demand": job_data["market_demand"],
        "match_score": round(min(score, 1.0) * 100), "match_level": match_level,
        "avg_salary_usd": job_data.get("avg_salary_usd", 0),
        "experience_levels": job_data.get("experience_levels", []),
        "description": job_data.get("description", ""),
        "reason": f"You match {round(jac*100)}% of required skills. Similarity: {round(cos*100)}%.",
    }

def rank_careers(user_skills, experience_years, career_interests):
    return sorted(
        [compute_score(user_skills, experience_years, n, d, career_interests) for n, d in JOB_ROLES.items()],
        key=lambda x: x["match_score"], reverse=True
    )

def do_gap_analysis(user_skills, job_data):
    required = set(job_data["required_skills"])
    critical = set(job_data["critical_skills"])
    user_set = set(user_skills)
    missing  = required - user_set
    nice     = set(job_data.get("nice_to_have", []))
    roadmap  = []
    for skill in sorted(missing & critical):
        roadmap.append({"phase": 1, "priority": "Critical", "skill": skill,
                        "courses": COURSE_CATALOG.get(skill, ["Search on Coursera/Udemy"]), "estimated_weeks": 6})
    for skill in sorted(missing - critical):
        roadmap.append({"phase": 2, "priority": "Intermediate", "skill": skill,
                        "courses": COURSE_CATALOG.get(skill, ["Search on Coursera/Udemy"]), "estimated_weeks": 4})
    return {
        "present_skills": sorted(required & user_set), "missing_skills": sorted(missing & critical) + sorted(missing - critical),
        "critical_gaps": sorted(missing & critical), "intermediate_gaps": sorted(missing - critical),
        "nice_to_have": sorted(nice - user_set),
        "readiness_score": round(len(required & user_set) / len(required), 2) if required else 0,
        "total_required": len(required), "total_missing": len(missing), "roadmap": roadmap,
    }

# ══════════════════════════════════════════════════════════
# ENDPOINTS
# ══════════════════════════════════════════════════════════

@app.route("/health", methods=["GET"])
def health():
    return jsonify({
        "status": "ok", "service": "CareerCraft AI", "ai_available": True,
        "dataset": {"skills": len(SKILL_CATALOG), "job_roles": len(JOB_ROLES), "courses": len(COURSE_CATALOG)}
    }), 200

@app.route("/career-recommendations", methods=["POST"])
def career_recommendations():
    data = request.get_json(silent=True) or {}
    ranked = rank_careers(
        parse_skills(data.get("user_skills", "")),
        int(data.get("experience_years", 0)),
        parse_skills(data.get("career_interests", []))
    )
    return jsonify({"recommendations": ranked}), 200

@app.route("/job-match", methods=["POST"])
def job_match():
    data = request.get_json(silent=True) or {}
    user_skills = parse_skills(data.get("user_skills", ""))
    job_title   = str(data.get("job_title", "")).strip()
    job_skills  = parse_skills(data.get("job_skills", ""))
    if not user_skills or not job_skills:
        return jsonify({"match_score": 0}), 200
    job_data = JOB_ROLES.get(job_title, {"required_skills": job_skills, "critical_skills": [], "market_demand": 0.80})
    cos = cosine_similarity(
        build_weighted_vector(user_skills, job_data["critical_skills"], 0),
        build_weighted_vector(job_data["required_skills"], job_data["critical_skills"], 0)
    )
    jac   = jaccard_similarity(set(user_skills), set(job_skills))
    score = round((COSINE_WEIGHT*cos + JACCARD_WEIGHT*jac) * 100 / (COSINE_WEIGHT + JACCARD_WEIGHT))
    return jsonify({"match_score": min(100, max(0, score))}), 200

@app.route("/advice", methods=["POST"])
def advice():
    data     = request.get_json(silent=True) or {}
    question = str(data.get("question", "")).strip()
    context  = str(data.get("context", "")).strip()
    if not question:
        return jsonify({"answer": None}), 200
    user_skills = []
    if "skills:" in context.lower():
        user_skills = [s.strip() for s in context.lower().split("skills:")[-1].split(",") if s.strip()]
    q = question.lower()
    if any(w in q for w in ["best career","career path","recommend","suitable"]):
        ranked = rank_careers(user_skills, 0, [])
        top    = ranked[:3]
        answer = (f"Based on your skills, top paths: {', '.join(r['title'] for r in top)}. "
                  f"Best match: {top[0]['title']} at {top[0]['match_score']}% (avg ${top[0].get('avg_salary_usd',0):,}/yr).")
    elif any(w in q for w in ["skill gap","missing","need to learn","lacking"]):
        for jn, jd in JOB_ROLES.items():
            if jn.lower() in q:
                gaps = do_gap_analysis(user_skills, jd)
                return jsonify({"answer": f"For {jn}, readiness: {round(gaps['readiness_score']*100)}%. Critical: {', '.join(gaps['critical_gaps']) or 'none'}."}), 200
        answer = "Update your profile skills so CareerCraft can compare them against your target role."
    elif any(w in q for w in ["course","learn","study","resource"]):
        answer = "CareerCraft recommends courses from Coursera, Udemy, edX, HuggingFace & Fast.ai tailored to your skill gaps. Check 'My Roadmap'."
    elif any(w in q for w in ["salary","pay","income","earn"]):
        top5 = sorted(JOB_ROLES.items(), key=lambda x: x[1]["avg_salary_usd"], reverse=True)[:5]
        answer = "Top paying roles: " + ", ".join(f"{n} (${d['avg_salary_usd']:,})" for n, d in top5)
    elif any(w in q for w in ["job","roles","positions"]):
        answer = f"CareerCraft covers {len(JOB_ROLES)} job roles across Tech, AI/ML, Security, Design & more."
    else:
        answer = "CareerCraft helps identify your best career path, skill gaps, and personalized learning roadmap. Update your profile for accurate recommendations."
    return jsonify({"answer": answer}), 200

@app.route("/suggested-skills", methods=["POST"])
def suggested_skills():
    data        = request.get_json(silent=True) or {}
    career_path = str(data.get("career_path", "")).strip()
    jd = JOB_ROLES.get(career_path) or next((d for n, d in JOB_ROLES.items() if career_path.lower() in n.lower()), None)
    if jd:
        return jsonify({"skills": jd["required_skills"], "critical": jd["critical_skills"], "nice_to_have": jd.get("nice_to_have", [])}), 200
    return jsonify({"skills": ["Communication","Problem Solving","Critical Thinking","Teamwork","Time Management"]}), 200

@app.route("/resume-review", methods=["POST"])
def resume_review():
    data        = request.get_json(silent=True) or {}
    resume_text = str(data.get("resume_text", "")).strip().lower()
    if not resume_text:
        return jsonify({"score": 0, "strengths": [], "improvements": [], "summary": "No resume text provided."}), 200
    found = [s for s in SKILL_CATALOG if s.lower() in resume_text]
    score = min(100, 40 + len(found) * 3)
    strengths, improvements = [], []
    if len(found) >= 6:      strengths.append("Strong technical skill coverage.")
    if "experience" in resume_text: strengths.append("Work experience is mentioned.")
    if any(w in resume_text for w in ["education","university","degree"]): strengths.append("Education background present.")
    if "project" in resume_text:    strengths.append("Projects section adds credibility.")
    if any(w in resume_text for w in ["github","portfolio"]): strengths.append("GitHub/portfolio link included.")
    if len(found) < 5:       improvements.append("Add more relevant technical skills.")
    if not any(w in resume_text for w in ["github","portfolio"]): improvements.append("Include a GitHub or portfolio link.")
    if not any(w in resume_text for w in ["achievement","impact"]): improvements.append("Quantify achievements with numbers.")
    if len(resume_text) < 300: improvements.append("Resume is short — add more detail.")
    if not any(w in resume_text for w in ["certificate","certified"]): improvements.append("Add relevant certifications.")
    return jsonify({
        "score": score,
        "strengths":    strengths or ["Resume submitted successfully."],
        "improvements": improvements or ["Keep your resume updated."],
        "summary": f"Found {len(found)} skills: {', '.join(found[:6])}{'...' if len(found)>6 else ''}. Score: {score}/100.",
    }), 200

@app.route("/interview-simulate", methods=["POST"])
def interview_simulate():
    data         = request.get_json(silent=True) or {}
    job_title    = str(data.get("job_title", "")).strip()
    conversation = data.get("conversation", [])
    jd = JOB_ROLES.get(job_title) or next((d for n, d in JOB_ROLES.items() if job_title.lower() in n.lower()), None)
    questions = jd["interview_questions"] if jd else [
        "Tell me about yourself.", "What are your strongest skills?",
        "Describe a challenging project.", "How do you handle pressure?", "Where do you see yourself in 5 years?"
    ]
    FEEDBACK = ["Good answer. Clear thinking shown.","Nice — add specific examples next time.",
                "Solid. Consider quantifying your impact.","Good. Use STAR format for structure.","Well done — show more technical depth."]
    qn = len(conversation) + 1
    if qn > len(questions):
        return jsonify({"question": None, "feedback": None, "question_number": qn, "completed": True,
                        "overall_feedback": f"Interview complete! {len(conversation)} questions answered for {job_title}. Practice STAR-format answers."}), 200
    return jsonify({"question": questions[qn-1], "feedback": FEEDBACK[min(len(conversation)-1,4)] if conversation else None,
                    "question_number": qn, "completed": False, "overall_feedback": None}), 200

@app.route("/skills/categories", methods=["GET"])
def skills_by_category():
    cats: Dict[str, List] = {}
    for skill, meta in SKILL_CATALOG_FULL.items():
        cat = meta["category"]
        cats.setdefault(cat, []).append({"skill": skill, "demand": meta["demand"]})
    for cat in cats:
        cats[cat].sort(key=lambda x: x["demand"], reverse=True)
    return jsonify({"categories": cats, "total_skills": len(SKILL_CATALOG)}), 200

@app.route("/salary-benchmark", methods=["POST"])
def salary_benchmark():
    data  = request.get_json(silent=True) or {}
    title = str(data.get("job_title", "")).strip()
    level = str(data.get("level", "mid")).lower()
    region= str(data.get("region", "USA")).strip()
    jd = JOB_ROLES.get(title) or next((d for n, d in JOB_ROLES.items() if title.lower() in n.lower()), None)
    if not jd:
        return jsonify({"error": "Job title not found"}), 404
    mult   = SALARY_BENCHMARKS.get(region, SALARY_BENCHMARKS["USA"]).get(level, 1.0)
    salary = round(jd["avg_salary_usd"] * mult)
    return jsonify({"job_title": title, "level": level, "region": region,
                    "salary_usd": salary, "salary_range": {"min": round(salary*0.85), "max": round(salary*1.15)},
                    "base_mid_usa": jd["avg_salary_usd"]}), 200

# ══════════════════════════════════════════════════════════
# ZAKI CHATBOT
# ══════════════════════════════════════════════════════════

ZAKI_INTENTS = {
    "greeting":  ["hi","hello","hey","marhaba","ahlan","هاي","اهلا","مرحبا","السلام","صباح","مساء"],
    "career":    ["career","path","recommend","suitable","best","مسار","وظيفة","شغل","كاريير"],
    "skills":    ["skill","learn","missing","gap","improve","مهارة","تعلم","ناقص"],
    "courses":   ["course","study","resource","udemy","coursera","كورس","دراسة"],
    "resume":    ["resume","cv","سيرة","رزيومي"],
    "interview": ["interview","انترفيو","مقابلة"],
    "salary":    ["salary","pay","earn","income","راتب","مرتب"],
    "jobs":      ["find job","vacancy","فرصة","وظائف","فرص عمل"],
    "thanks":    ["thanks","thank","شكرا","شكراً","ممتاز","تمام","great","ok"],
    "farewell":  ["bye","goodbye","مع السلامة","باي"],
}

ZAKI_REPLIES = {
    "greeting":  ["أهلاً وسهلاً! 😊 أنا زاكي مساعدك المهني. ممكن أساعدك في:\n• إيجاد مسارك المهني\n• تحليل مهاراتك\n• فرص وظيفية\n• تحضير للانترفيو"],
    "career":    [f"ممتاز! 🌟 عندنا **{len(JOB_ROLES)} مسار مهني** مختلف! قولي مهاراتك وهقيسلك التوافق فوراً."],
    "skills":    [f"💪 قاعدة البيانات بتاعتنا فيها **{len(SKILL_CATALOG)} مهارة** في 12 تخصص!\nروح على 'Skill Gap Analysis' واختار مسارك."],
    "courses":   ["📚 عندنا كورسات من Coursera, Udemy, edX, Fast.ai & HuggingFace!\nاضغط على 'My Roadmap' للتفاصيل."],
    "resume":    ["📄 ارفع الـ CV في 'Resume Review' وهتاخد:\n✅ تقييم من 100\n✅ نقاط القوة\n✅ توصيات التحسين"],
    "interview": [f"🎤 عندنا Interview Simulator لـ **{len(JOB_ROLES)} وظيفة** مع أسئلة حقيقية وfeedback فوري!"],
    "salary":    ["💰 أعلى رواتب (Mid-level, USA):\n• AI Engineer: $160k\n• Cloud Architect: $165k\n• ML Engineer: $155k\n• NLP Engineer: $150k\nعندنا بيانات لـ 4 مناطق مختلفة!"],
    "jobs":      [f"💼 CareerCraft بيغطي **{len(JOB_ROLES)} مسار** شاملين Data, AI, Security, Design وأكتر!"],
    "thanks":    ["العفو! 😊 في حاجة تانية أقدر أساعدك فيها؟"],
    "farewell":  ["مع السلامة! 👋 بالتوفيق في مسيرتك المهنية! 🚀"],
    "default":   [f"أنا زاكي! 🤖 عندي **{len(JOB_ROLES)} مسار** و**{len(SKILL_CATALOG)} مهارة** في قاعدة البيانات. قولي إيه اللي محتاجه!"],
}

QUICK_REPLIES_MAP = {
    "greeting":  ["إيه أفضل مسار ليا؟","فرص وظيفية","تحليل مهاراتي"],
    "career":    ["تحليل الـ Skill Gap","شوفلي كورسات","فرص وظيفية"],
    "skills":    ["كورسات موصى بيها","مسارات مهنية مناسبة"],
    "courses":   ["مسارات مهنية","تحليل مهاراتي"],
    "resume":    ["تحليل مهاراتي","فرص وظيفية"],
    "interview": ["تحليل مهاراتي","كورسات موصى بيها"],
    "salary":    ["فرص وظيفية","إيه أفضل مسار ليا؟"],
    "jobs":      ["إيه أفضل مسار ليا؟","تحليل مهاراتي"],
    "thanks":    ["إيه أفضل مسار ليا؟","فرص وظيفية"],
    "farewell":  [],
    "default":   ["إيه أفضل مسار ليا؟","فرص وظيفية","تحليل مهاراتي","تحضير للانترفيو"],
}

def detect_intent(message: str) -> str:
    m = message.lower().strip()
    for intent, kws in ZAKI_INTENTS.items():
        if any(kw in m for kw in kws):
            return intent
    return "default"

def get_career_reply_with_skills(user_skills: List[str]) -> str:
    top3  = rank_careers(user_skills, 0, [])[:3]
    lines = ["بناءً على مهاراتك: 🎯\n"]
    for r in top3:
        bar = "█"*(r["match_score"]//10) + "░"*(10-r["match_score"]//10)
        lines.append(f"• **{r['title']}** — {r['match_score']}% | ${r.get('avg_salary_usd',0):,}/yr\n  {bar}")
    lines.append("\nاضغط على 'Career Recommendations' للتفاصيل!")
    return "\n".join(lines)

@app.route("/chatbot", methods=["POST"])
def chatbot():
    data        = request.get_json(silent=True) or {}
    message     = str(data.get("message", "")).strip()
    user_skills = parse_skills(data.get("user_skills", ""))
    if not message:
        return jsonify({"reply": None, "intent": None, "quick_replies": []}), 400
    intent = detect_intent(message)
    reply  = get_career_reply_with_skills(user_skills) if (intent == "career" and user_skills) else random.choice(ZAKI_REPLIES.get(intent, ZAKI_REPLIES["default"]))
    return jsonify({"reply": reply, "intent": intent,
                    "quick_replies": QUICK_REPLIES_MAP.get(intent, QUICK_REPLIES_MAP["default"]),
                    "zaki": True, "dataset_info": {"skills": len(SKILL_CATALOG), "roles": len(JOB_ROLES)}}), 200

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5000, debug=False)
